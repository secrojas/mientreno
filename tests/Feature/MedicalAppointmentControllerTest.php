<?php

namespace Tests\Feature;

use App\Enums\AppointmentStatus;
use App\Enums\AppointmentTaskType;
use App\Models\Doctor;
use App\Models\MedicalAppointment;
use App\Models\MedicalAppointmentTask;
use App\Models\MedicalDocument;
use App\Models\MedicalOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MedicalAppointmentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_requires_authentication(): void
    {
        $response = $this->get(route('medical.appointments.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_index_splits_appointments_into_sections(): void
    {
        $user = User::factory()->create();
        $upcoming = MedicalAppointment::factory()->for($user)->create();
        $awaiting = MedicalAppointment::factory()->for($user)->awaitingFollowUp()->create();
        $completed = MedicalAppointment::factory()->for($user)->completed()->create();
        MedicalAppointment::factory()->create();

        $response = $this->actingAs($user)->get(route('medical.appointments.index'));

        $response->assertOk();
        $response->assertViewIs('medical.appointments');
        $response->assertViewHas('upcomingAppointments', fn ($appointments) => $appointments->pluck('id')->all() === [$upcoming->id]);
        $response->assertViewHas('awaitingFollowUp', fn ($appointments) => $appointments->pluck('id')->all() === [$awaiting->id]);
        $response->assertViewHas('history', fn ($history) => $history->flatten()->pluck('id')->all() === [$completed->id]);
        $response->assertSee('¿Cómo te fue?');
    }

    public function test_index_shows_open_tasks_from_all_appointments(): void
    {
        $user = User::factory()->create();
        $appointment = MedicalAppointment::factory()->for($user)->completed()->create();
        $openTask = MedicalAppointmentTask::factory()->for($appointment, 'appointment')->create(['description' => 'Consultar cirujano']);
        MedicalAppointmentTask::factory()->for($appointment, 'appointment')->completed()->create();
        MedicalAppointmentTask::factory()->create();

        $response = $this->actingAs($user)->get(route('medical.appointments.index'));

        $response->assertViewHas('openTasks', fn ($tasks) => $tasks->pluck('id')->all() === [$openTask->id]);
        $response->assertSee('Consultar cirujano');
    }

    public function test_index_filters_history_by_doctor_and_specialty(): void
    {
        $user = User::factory()->create();
        $clinician = Doctor::factory()->for($user)->create(['specialty' => 'Clínica médica']);
        $surgeon = Doctor::factory()->for($user)->create(['specialty' => 'Cirugía']);
        $clinicianVisit = MedicalAppointment::factory()->for($user)->completed()->create(['doctor_id' => $clinician->id]);
        $surgeonVisit = MedicalAppointment::factory()->for($user)->completed()->create(['doctor_id' => $surgeon->id]);

        $this->actingAs($user)
            ->get(route('medical.appointments.index', ['doctor' => $clinician->id]))
            ->assertViewHas('history', fn ($history) => $history->flatten()->pluck('id')->all() === [$clinicianVisit->id]);

        $this->actingAs($user)
            ->get(route('medical.appointments.index', ['specialty' => 'Cirugía']))
            ->assertViewHas('history', fn ($history) => $history->flatten()->pluck('id')->all() === [$surgeonVisit->id]);
    }

    public function test_store_creates_appointment_using_doctor_address_as_default_location(): void
    {
        $user = User::factory()->create();
        $doctor = Doctor::factory()->for($user)->create(['address' => 'Av. Siempreviva 742']);

        $response = $this->actingAs($user)->post(route('medical.appointments.store'), [
            'doctor_id' => $doctor->id,
            'scheduled_at' => '2026-10-15 10:30',
            'reason' => 'Mostrar batería de estudios',
        ]);

        $response->assertRedirect(route('medical.appointments.index'));
        $response->assertSessionHas('status', 'appointment-created');
        $this->assertDatabaseHas('medical_appointments', [
            'user_id' => $user->id,
            'doctor_id' => $doctor->id,
            'location' => 'Av. Siempreviva 742',
            'reason' => 'Mostrar batería de estudios',
            'status' => AppointmentStatus::Scheduled->value,
            'scheduled_at' => '2026-10-15 10:30:00',
        ]);
    }

    public function test_store_can_create_a_new_doctor_inline(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('medical.appointments.store'), [
            'new_doctor_name' => 'Dra. Pérez',
            'new_doctor_specialty' => 'Cirugía general',
            'scheduled_at' => '2026-10-20 09:00',
        ]);

        $doctor = Doctor::where('user_id', $user->id)->where('name', 'Dra. Pérez')->first();
        $this->assertNotNull($doctor);
        $this->assertSame('Cirugía general', $doctor->specialty);
        $this->assertDatabaseHas('medical_appointments', ['user_id' => $user->id, 'doctor_id' => $doctor->id]);
    }

    public function test_store_requires_scheduled_at(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('medical.appointments.store'), [
            'reason' => 'Control',
        ]);

        $response->assertSessionHasErrors(['scheduled_at' => 'Ingresá la fecha y hora del turno.']);
    }

    public function test_store_rejects_doctor_from_other_user(): void
    {
        $user = User::factory()->create();
        $foreignDoctor = Doctor::factory()->create();

        $response = $this->actingAs($user)->post(route('medical.appointments.store'), [
            'doctor_id' => $foreignDoctor->id,
            'scheduled_at' => '2026-10-15 10:30',
        ]);

        $response->assertSessionHasErrors(['doctor_id']);
    }

    public function test_store_from_referral_links_and_completes_the_task(): void
    {
        $user = User::factory()->create();
        $visit = MedicalAppointment::factory()->for($user)->completed()->create();
        $referral = MedicalAppointmentTask::factory()->for($visit, 'appointment')->create([
            'type' => AppointmentTaskType::Referral,
            'description' => 'Consultar cirujano por pólipo vesicular',
        ]);

        $this->actingAs($user)->post(route('medical.appointments.store'), [
            'scheduled_at' => '2026-10-22 16:00',
            'reason' => 'Consultar cirujano por pólipo vesicular',
            'referred_from_id' => $visit->id,
            'referral_task_id' => $referral->id,
        ]);

        $followUp = MedicalAppointment::where('referred_from_id', $visit->id)->first();
        $this->assertNotNull($followUp);
        $referral->refresh();
        $this->assertSame($followUp->id, $referral->follow_up_appointment_id);
        $this->assertTrue($referral->isCompleted());
    }

    public function test_store_rejects_referral_task_from_other_user(): void
    {
        $user = User::factory()->create();
        $foreignTask = MedicalAppointmentTask::factory()->create();

        $response = $this->actingAs($user)->post(route('medical.appointments.store'), [
            'scheduled_at' => '2026-10-22 16:00',
            'referral_task_id' => $foreignTask->id,
        ]);

        $response->assertSessionHasErrors(['referral_task_id']);
        $this->assertNull($foreignTask->fresh()->follow_up_appointment_id);
    }

    public function test_update_completes_appointment_with_observations_links_and_new_tasks(): void
    {
        $user = User::factory()->create();
        $appointment = MedicalAppointment::factory()->for($user)->awaitingFollowUp()->create();
        $documents = MedicalDocument::factory()->count(2)->create(['user_id' => $user->id]);
        $order = MedicalOrder::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->put(route('medical.appointments.update', $appointment), [
            'scheduled_at' => $appointment->scheduled_at->format('Y-m-d H:i'),
            'status' => AppointmentStatus::Completed->value,
            'observations' => "Colesterol alto.\nPólipo vesicular.",
            'document_ids' => $documents->pluck('id')->all(),
            'order_ids' => [$order->id],
            'new_tasks' => [
                ['type' => 'medication', 'description' => 'Atorvastatina 10 mg', 'due_date' => ''],
                ['type' => 'referral', 'description' => 'Consultar cirujano', 'due_date' => '2026-11-01'],
                ['type' => 'other', 'description' => '', 'due_date' => ''],
            ],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', 'appointment-updated');

        $appointment->refresh();
        $this->assertSame(AppointmentStatus::Completed, $appointment->status);
        $this->assertSame("Colesterol alto.\nPólipo vesicular.", $appointment->observations);
        $this->assertCount(2, $appointment->documents);
        $this->assertSame($appointment->id, $order->fresh()->medical_appointment_id);
        $this->assertSame(['Atorvastatina 10 mg', 'Consultar cirujano'], $appointment->tasks()->orderBy('id')->pluck('description')->all());
        $this->assertSame('2026-11-01', $appointment->tasks()->where('type', 'referral')->first()->due_date->format('Y-m-d'));
    }

    public function test_update_unlinks_orders_that_are_no_longer_selected(): void
    {
        $user = User::factory()->create();
        $appointment = MedicalAppointment::factory()->for($user)->completed()->create();
        $order = MedicalOrder::factory()->create(['user_id' => $user->id, 'medical_appointment_id' => $appointment->id]);

        $this->actingAs($user)->put(route('medical.appointments.update', $appointment), [
            'scheduled_at' => $appointment->scheduled_at->format('Y-m-d H:i'),
            'status' => AppointmentStatus::Completed->value,
        ]);

        $this->assertNull($order->fresh()->medical_appointment_id);
    }

    public function test_update_rejects_documents_from_other_users(): void
    {
        $user = User::factory()->create();
        $appointment = MedicalAppointment::factory()->for($user)->create();
        $foreignDocument = MedicalDocument::factory()->create();

        $response = $this->actingAs($user)->put(route('medical.appointments.update', $appointment), [
            'scheduled_at' => $appointment->scheduled_at->format('Y-m-d H:i'),
            'status' => AppointmentStatus::Completed->value,
            'document_ids' => [$foreignDocument->id],
        ]);

        $response->assertSessionHasErrors(['document_ids.0']);
    }

    public function test_update_is_forbidden_for_other_users(): void
    {
        $appointment = MedicalAppointment::factory()->create();

        $response = $this->actingAs(User::factory()->create())->put(route('medical.appointments.update', $appointment), [
            'scheduled_at' => '2026-10-15 10:30',
            'status' => AppointmentStatus::Completed->value,
        ]);

        $response->assertForbidden();
    }

    public function test_destroy_deletes_appointment_and_its_tasks(): void
    {
        $user = User::factory()->create();
        $appointment = MedicalAppointment::factory()->for($user)->create();
        $task = MedicalAppointmentTask::factory()->for($appointment, 'appointment')->create();

        $response = $this->actingAs($user)->delete(route('medical.appointments.destroy', $appointment));

        $response->assertRedirect();
        $this->assertModelMissing($appointment);
        $this->assertModelMissing($task);
    }

    public function test_destroy_is_forbidden_for_other_users(): void
    {
        $appointment = MedicalAppointment::factory()->create();

        $response = $this->actingAs(User::factory()->create())->delete(route('medical.appointments.destroy', $appointment));

        $response->assertForbidden();
        $this->assertModelExists($appointment);
    }

    public function test_calendar_downloads_ics_event(): void
    {
        $user = User::factory()->create();
        $doctor = Doctor::factory()->for($user)->create(['name' => 'Dr. Gómez', 'specialty' => 'Clínica médica']);
        $appointment = MedicalAppointment::factory()->for($user)->create([
            'doctor_id' => $doctor->id,
            'scheduled_at' => '2026-10-15 10:30',
            'location' => 'Av. Corrientes 1234, CABA',
            'reason' => 'Control; resultados',
        ]);

        $response = $this->actingAs($user)->get(route('medical.appointments.calendar', $appointment));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/calendar; charset=utf-8');
        $this->assertStringContainsString('attachment; filename="turno-2026-10-15-', $response->headers->get('content-disposition'));

        $content = $response->getContent();
        $this->assertStringContainsString("BEGIN:VCALENDAR\r\n", $content);
        $this->assertStringContainsString("DTSTART:20261015T103000\r\n", $content);
        $this->assertStringContainsString("DTEND:20261015T113000\r\n", $content);
        $this->assertStringContainsString('SUMMARY:Turno médico: Dr. Gómez — Clínica médica', $content);
        $this->assertStringContainsString('LOCATION:Av. Corrientes 1234\, CABA', $content);
        $this->assertStringContainsString('Motivo: Control\; resultados', $content);
        $this->assertStringContainsString('TRIGGER:-P1D', $content);

        foreach (explode("\r\n", $content) as $line) {
            $this->assertLessThanOrEqual(75, strlen($line));
        }
    }

    public function test_calendar_is_forbidden_for_other_users(): void
    {
        $appointment = MedicalAppointment::factory()->create();

        $response = $this->actingAs(User::factory()->create())->get(route('medical.appointments.calendar', $appointment));

        $response->assertForbidden();
    }

    public function test_order_can_be_uploaded_linked_to_an_appointment(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();
        $appointment = MedicalAppointment::factory()->for($user)->completed()->create();

        $this->actingAs($user)->post(route('medical.orders.store'), [
            'title' => 'Receta atorvastatina',
            'file' => UploadedFile::fake()->image('receta.jpg'),
            'medical_appointment_id' => $appointment->id,
        ]);

        $this->assertDatabaseHas('medical_orders', [
            'user_id' => $user->id,
            'title' => 'Receta atorvastatina',
            'medical_appointment_id' => $appointment->id,
        ]);
    }

    public function test_order_cannot_be_linked_to_other_users_appointment(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();
        $foreignAppointment = MedicalAppointment::factory()->create();

        $response = $this->actingAs($user)->post(route('medical.orders.store'), [
            'title' => 'Receta',
            'file' => UploadedFile::fake()->image('receta.jpg'),
            'medical_appointment_id' => $foreignAppointment->id,
        ]);

        $response->assertSessionHasErrors(['medical_appointment_id']);
    }
}

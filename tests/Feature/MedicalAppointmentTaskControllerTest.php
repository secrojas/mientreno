<?php

namespace Tests\Feature;

use App\Enums\AppointmentTaskType;
use App\Models\MedicalAppointment;
use App\Models\MedicalAppointmentTask;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicalAppointmentTaskControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_adds_task_to_appointment(): void
    {
        $user = User::factory()->create();
        $appointment = MedicalAppointment::factory()->for($user)->completed()->create();

        $response = $this->actingAs($user)->post(route('medical.appointments.tasks.store', $appointment), [
            'type' => AppointmentTaskType::Study->value,
            'description' => 'Perfil lipídico',
            'due_date' => '2026-12-20',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', 'task-created');
        $this->assertDatabaseHas('medical_appointment_tasks', [
            'medical_appointment_id' => $appointment->id,
            'type' => 'study',
            'description' => 'Perfil lipídico',
            'completed_at' => null,
        ]);
    }

    public function test_store_validates_type_and_description(): void
    {
        $user = User::factory()->create();
        $appointment = MedicalAppointment::factory()->for($user)->create();

        $response = $this->actingAs($user)->post(route('medical.appointments.tasks.store', $appointment), [
            'type' => 'surgery',
            'description' => '',
        ]);

        $response->assertSessionHasErrors(['type', 'description' => 'Describí la indicación.']);
    }

    public function test_store_is_forbidden_for_other_users(): void
    {
        $appointment = MedicalAppointment::factory()->create();

        $response = $this->actingAs(User::factory()->create())->post(route('medical.appointments.tasks.store', $appointment), [
            'type' => AppointmentTaskType::Other->value,
            'description' => 'Algo',
        ]);

        $response->assertForbidden();
    }

    public function test_toggle_marks_task_as_done_and_back_to_pending(): void
    {
        $user = User::factory()->create();
        $appointment = MedicalAppointment::factory()->for($user)->create();
        $task = MedicalAppointmentTask::factory()->for($appointment, 'appointment')->create();

        $this->actingAs($user)->patch(route('medical.appointments.tasks.toggle', $task))->assertRedirect();
        $this->assertTrue($task->fresh()->isCompleted());

        $this->actingAs($user)->patch(route('medical.appointments.tasks.toggle', $task))->assertRedirect();
        $this->assertFalse($task->fresh()->isCompleted());
    }

    public function test_toggle_is_forbidden_for_other_users(): void
    {
        $task = MedicalAppointmentTask::factory()->create();

        $response = $this->actingAs(User::factory()->create())->patch(route('medical.appointments.tasks.toggle', $task));

        $response->assertForbidden();
        $this->assertFalse($task->fresh()->isCompleted());
    }

    public function test_destroy_deletes_task(): void
    {
        $user = User::factory()->create();
        $appointment = MedicalAppointment::factory()->for($user)->create();
        $task = MedicalAppointmentTask::factory()->for($appointment, 'appointment')->create();

        $response = $this->actingAs($user)->delete(route('medical.appointments.tasks.destroy', $task));

        $response->assertRedirect();
        $this->assertModelMissing($task);
    }

    public function test_destroy_is_forbidden_for_other_users(): void
    {
        $task = MedicalAppointmentTask::factory()->create();

        $response = $this->actingAs(User::factory()->create())->delete(route('medical.appointments.tasks.destroy', $task));

        $response->assertForbidden();
        $this->assertModelExists($task);
    }

    public function test_overdue_task_is_detected(): void
    {
        $overdue = MedicalAppointmentTask::factory()->make(['due_date' => today()->subDay()]);
        $completedOverdue = MedicalAppointmentTask::factory()->completed()->make(['due_date' => today()->subDay()]);
        $dueToday = MedicalAppointmentTask::factory()->make(['due_date' => today()]);

        $this->assertTrue($overdue->isOverdue());
        $this->assertFalse($completedOverdue->isOverdue());
        $this->assertFalse($dueToday->isOverdue());
    }
}

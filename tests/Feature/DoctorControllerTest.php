<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DoctorControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_creates_doctor_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('medical.doctors.store'), [
            'name' => 'Dr. Juan Pérez',
            'specialty' => 'Cardiología',
            'phone' => '11-1234-5678',
            'email' => 'juan.perez@example.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', 'doctor-created');

        $this->assertDatabaseHas('doctors', [
            'user_id' => $user->id,
            'name' => 'Dr. Juan Pérez',
            'specialty' => 'Cardiología',
        ]);
    }

    public function test_store_requires_name(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('medical.doctors.store'), []);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_update_modifies_doctor(): void
    {
        $user = User::factory()->create();
        $doctor = Doctor::factory()->for($user)->create(['name' => 'Dr. Viejo']);

        $response = $this->actingAs($user)->put(route('medical.doctors.update', $doctor), [
            'name' => 'Dr. Nuevo',
            'specialty' => 'Clínica Médica',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('doctors', [
            'id' => $doctor->id,
            'name' => 'Dr. Nuevo',
        ]);
    }

    public function test_update_is_forbidden_for_other_users(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $doctor = Doctor::factory()->for($owner)->create();

        $response = $this->actingAs($other)->put(route('medical.doctors.update', $doctor), [
            'name' => 'Hackeado',
        ]);

        $response->assertForbidden();
        $this->assertNotSame('Hackeado', $doctor->fresh()->name);
    }

    public function test_destroy_deletes_doctor(): void
    {
        $user = User::factory()->create();
        $doctor = Doctor::factory()->for($user)->create();

        $response = $this->actingAs($user)->delete(route('medical.doctors.destroy', $doctor));

        $response->assertRedirect();
        $response->assertSessionHas('status', 'doctor-deleted');
        $this->assertModelMissing($doctor);
    }

    public function test_destroy_is_forbidden_for_other_users(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $doctor = Doctor::factory()->for($owner)->create();

        $response = $this->actingAs($other)->delete(route('medical.doctors.destroy', $doctor));

        $response->assertForbidden();
        $this->assertModelExists($doctor);
    }
}

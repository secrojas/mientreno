<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Workout;
use App\Models\Business;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh');
    }

    /** @test */
    public function user_can_create_workout()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('workouts.store'), [
            'date' => '2025-12-30',
            'type' => 'easy_run',
            'distance' => 10.5,
            'duration' => 3600, // 1 hora en segundos
            'difficulty' => 3, // 1-5 scale
            'notes' => 'Great run!',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('workouts', [
            'user_id' => $user->id,
            'type' => 'easy_run',
            'distance' => 10.5,
            // status será 'completed' si se proporcionan distance y duration
            'status' => 'completed',
        ]);
    }

    /** @test */
    public function user_can_view_their_workouts()
    {
        $user = User::factory()->create();
        Workout::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('workouts.index'));

        $response->assertStatus(200);
        $response->assertViewIs('workouts.index');
        $response->assertViewHas('workouts');
    }

    /** @test */
    public function user_can_update_workout()
    {
        $user = User::factory()->create();
        $workout = Workout::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->put(route('workouts.update', $workout), [
            'date' => $workout->date->format('Y-m-d'),
            'type' => 'tempo',
            'distance' => 15,
            'duration' => 4500,
            'difficulty' => 5, // 1-5 scale (hard)
            'notes' => 'Updated notes',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('workouts', [
            'id' => $workout->id,
            'type' => 'tempo',
            'distance' => 15,
        ]);
    }

    /** @test */
    public function user_can_delete_workout()
    {
        $user = User::factory()->create();
        $workout = Workout::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('workouts.destroy', $workout));

        $response->assertRedirect();
        $this->assertDatabaseMissing('workouts', ['id' => $workout->id]);
    }

    /** @test */
    public function user_can_mark_workout_as_completed()
    {
        $user = User::factory()->create();
        $workout = Workout::factory()->create([
            'user_id' => $user->id,
            'status' => 'planned',
        ]);

        $response = $this->actingAs($user)->post(route('workouts.mark-completed', $workout), [
            'distance' => 10.5,
            'duration' => 3600,
            'difficulty' => 3, // 1-5 scale
            'notes' => 'Completed!',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('workouts', [
            'id' => $workout->id,
            'status' => 'completed',
            'distance' => 10.5,
        ]);
    }

    /** @test */
    public function user_can_mark_workout_as_skipped()
    {
        $user = User::factory()->create();
        $workout = Workout::factory()->create([
            'user_id' => $user->id,
            'status' => 'planned',
        ]);

        $response = $this->actingAs($user)->post(route('workouts.mark-skipped', $workout));

        $response->assertRedirect();
        $this->assertDatabaseHas('workouts', [
            'id' => $workout->id,
            'status' => 'skipped',
        ]);
    }

    /** @test */
    public function user_cannot_view_other_users_workouts()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $workout = Workout::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->get(route('workouts.edit', $workout));

        $response->assertStatus(403);
    }

    /** @test */
    public function user_cannot_delete_other_users_workouts()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $workout = Workout::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->delete(route('workouts.destroy', $workout));

        $response->assertStatus(403);
        $this->assertDatabaseHas('workouts', ['id' => $workout->id]);
    }

    /** @test */
    public function workout_calculates_pace_automatically()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('workouts.store'), [
            'date' => '2025-12-30',
            'type' => 'easy_run',
            'distance' => 10, // 10 km
            'duration' => 3000, // 50 minutos (3000 segundos)
            'difficulty' => 3, // 1-5 scale
        ]);

        $workout = Workout::where('user_id', $user->id)->first();

        // Pace debe ser 300 segundos por km (5:00 min/km)
        $this->assertEquals(300, $workout->avg_pace);
    }

    /** @test */
    public function guest_cannot_access_workouts()
    {
        $response = $this->get(route('workouts.index'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function workout_requires_date()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('workouts.store'), [
            'type' => 'easy_run',
            'distance' => 10,
            'duration' => 3600,
        ]);

        $response->assertSessionHasErrors('date');
    }

    /** @test */
    public function workout_requires_type()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('workouts.store'), [
            'date' => '2025-12-30',
            'distance' => 10,
            'duration' => 3600,
        ]);

        $response->assertSessionHasErrors('type');
    }

    /** @test */
    public function workout_type_must_be_valid()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('workouts.store'), [
            'date' => '2025-12-30',
            'type' => 'invalid_type',
            'distance' => 10,
            'duration' => 3600,
        ]);

        $response->assertSessionHasErrors('type');
    }
}

<?php

namespace Tests\Feature;

use App\Models\Shoe;
use App\Models\User;
use App\Models\Workout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkoutShoeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, mixed>
     */
    private function workoutPayload(array $overrides = []): array
    {
        return array_merge([
            'date' => now()->format('Y-m-d'),
            'type' => 'easy_run',
            'status' => 'completed',
            'distance' => 10,
            'duration' => 3000,
            'difficulty' => 3,
        ], $overrides);
    }

    public function test_create_form_preselects_default_shoe(): void
    {
        $user = User::factory()->create();
        Shoe::factory()->for($user)->create(['model' => 'Otra']);
        $default = Shoe::factory()->for($user)->default()->create(['model' => 'Pegasus 41']);
        Shoe::factory()->for($user)->retired()->create(['model' => 'Gastada']);

        $response = $this->actingAs($user)->get(route('workouts.create'));

        $response->assertOk();
        $response->assertViewHas('defaultShoeId', $default->id);
        $response->assertViewHas('shoes', fn ($shoes) => $shoes->count() === 2);
        $response->assertDontSee('Gastada');
    }

    public function test_create_form_invites_to_add_shoes_when_there_are_none(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('workouts.create'))->assertSee('Agregá tu primer par');
    }

    public function test_store_assigns_shoe_and_adds_km(): void
    {
        $user = User::factory()->create();
        $shoe = Shoe::factory()->for($user)->create(['initial_km' => 5]);

        $this->actingAs($user)->post(route('workouts.store'), $this->workoutPayload(['shoe_id' => $shoe->id]))->assertRedirect();

        $this->assertDatabaseHas('workouts', ['user_id' => $user->id, 'shoe_id' => $shoe->id]);
        $this->assertEquals(15, $shoe->fresh()->totalKm());
    }

    public function test_store_rejects_shoe_from_other_user(): void
    {
        $user = User::factory()->create();
        $foreignShoe = Shoe::factory()->create();

        $response = $this->actingAs($user)->post(route('workouts.store'), $this->workoutPayload(['shoe_id' => $foreignShoe->id]));

        $response->assertSessionHasErrors(['shoe_id']);
        $this->assertDatabaseMissing('workouts', ['shoe_id' => $foreignShoe->id]);
    }

    public function test_update_can_change_shoe_and_edit_form_keeps_retired_assigned_shoe(): void
    {
        $user = User::factory()->create();
        $retired = Shoe::factory()->for($user)->retired()->create(['model' => 'Gastada']);
        $newShoe = Shoe::factory()->for($user)->create();
        $workout = Workout::factory()->completed()->for($user)->create(['shoe_id' => $retired->id]);

        $this->actingAs($user)->get(route('workouts.edit', $workout))->assertSee('Gastada');

        $this->actingAs($user)->put(route('workouts.update', $workout), $this->workoutPayload(['shoe_id' => $newShoe->id]))->assertRedirect();

        $this->assertSame($newShoe->id, $workout->fresh()->shoe_id);
    }

    public function test_mark_completed_assigns_shoe(): void
    {
        $user = User::factory()->create();
        $shoe = Shoe::factory()->for($user)->default()->create();
        $workout = Workout::factory()->planned()->for($user)->create(['distance' => 12]);

        $this->actingAs($user)->get(route('workouts.mark-completed', $workout))->assertViewHas('defaultShoeId', $shoe->id);

        $this->actingAs($user)->post(route('workouts.mark-completed', $workout), [
            'distance' => 12,
            'duration' => 3600,
            'difficulty' => 3,
            'shoe_id' => $shoe->id,
        ])->assertRedirect();

        $this->assertSame($shoe->id, $workout->fresh()->shoe_id);
        $this->assertEquals(12, $shoe->fresh()->totalKm());
    }
}

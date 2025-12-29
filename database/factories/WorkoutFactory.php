<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Workout;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Workout>
 */
class WorkoutFactory extends Factory
{
    protected $model = Workout::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $distance = fake()->randomFloat(2, 5, 20); // Entre 5 y 20 km
        $duration = fake()->numberBetween(1800, 7200); // Entre 30min y 2 horas (en segundos)
        $avgPace = $distance > 0 ? round($duration / $distance) : null;

        return [
            'user_id' => User::factory(),
            'date' => fake()->dateTimeBetween('-3 months', 'now'),
            'type' => fake()->randomElement([
                'easy_run',
                'intervals',
                'tempo',
                'long_run',
                'recovery',
                'training_run',
            ]),
            'status' => fake()->randomElement(['planned', 'completed', 'skipped']),
            'distance' => $distance,
            'duration' => $duration,
            'avg_pace' => $avgPace,
            'difficulty' => fake()->numberBetween(1, 5), // 1-5 scale
            'notes' => fake()->optional(0.6)->sentence(),
            'race_id' => null,
        ];
    }

    /**
     * Indicate that the workout is planned.
     */
    public function planned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'planned',
        ]);
    }

    /**
     * Indicate that the workout is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    /**
     * Indicate that the workout is skipped.
     */
    public function skipped(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'skipped',
        ]);
    }

    /**
     * Indicate that the workout is an easy run.
     */
    public function easyRun(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'easy_run',
            'difficulty' => 2, // 1-5 scale (easy)
            'distance' => fake()->randomFloat(2, 8, 12),
        ]);
    }

    /**
     * Indicate that the workout is a long run.
     */
    public function longRun(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'long_run',
            'difficulty' => 5, // 1-5 scale (hard)
            'distance' => fake()->randomFloat(2, 15, 30),
        ]);
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Goal>
 */
class GoalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'race_id' => null,
            'type' => 'distance',
            'title' => fake()->randomElement([
                'Correr 100km este mes',
                'Mejorar mi pace promedio',
                'Entrenar 4 veces por semana',
            ]),
            'description' => fake()->optional(0.7)->sentence(),
            'target_value' => ['distance' => 100, 'period' => 'month'],
            'target_date' => fake()->dateTimeBetween('+1 month', '+6 months'),
            'start_date' => fake()->dateTimeBetween('-1 month', 'now'),
            'status' => 'active',
            'progress' => [
                'current_value' => 0,
                'percentage' => 0,
            ],
        ];
    }

    /**
     * Indicate that the goal is for a race.
     */
    public function raceGoal(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'race',
            'race_id' => \App\Models\Race::factory(),
            'title' => 'Completar carrera en tiempo objetivo',
            'target_value' => ['time' => fake()->numberBetween(3600, 14400)], // 1h - 4h
        ]);
    }

    /**
     * Indicate that the goal is for distance.
     */
    public function distanceGoal(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'distance',
            'race_id' => null,
            'title' => fake()->randomElement([
                'Correr 100km este mes',
                'Alcanzar 50km esta semana',
            ]),
            'target_value' => [
                'distance' => fake()->randomElement([50, 100, 200]),
                'period' => fake()->randomElement(['week', 'month', 'year']),
            ],
        ]);
    }

    /**
     * Indicate that the goal is for pace improvement.
     */
    public function paceGoal(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'pace',
            'race_id' => null,
            'title' => 'Mejorar pace promedio',
            'target_value' => ['pace' => fake()->numberBetween(240, 360)], // 4:00 - 6:00 min/km
        ]);
    }

    /**
     * Indicate that the goal is for training frequency.
     */
    public function frequencyGoal(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'frequency',
            'race_id' => null,
            'title' => fake()->randomElement([
                'Entrenar 4 veces por semana',
                'Hacer 12 sesiones este mes',
            ]),
            'target_value' => [
                'sessions' => fake()->numberBetween(3, 5),
                'period' => fake()->randomElement(['week', 'month']),
            ],
        ]);
    }

    /**
     * Indicate that the goal is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the goal is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'progress' => [
                'current_value' => $attributes['target_value'],
                'percentage' => 100,
            ],
        ]);
    }

    /**
     * Indicate that the goal is abandoned.
     */
    public function abandoned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'abandoned',
        ]);
    }
}

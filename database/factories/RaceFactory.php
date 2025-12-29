<?php

namespace Database\Factories;

use App\Models\Race;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Race>
 */
class RaceFactory extends Factory
{
    protected $model = Race::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $distances = [5, 10, 21.0975, 42.195]; // 5K, 10K, Media, Maratón
        $distance = fake()->randomElement($distances);

        return [
            'user_id' => User::factory(),
            'name' => fake()->randomElement([
                'Maratón de Buenos Aires',
                'Carrera 10K',
                'Media Maratón',
                '5K Solidaria',
            ]),
            'distance' => $distance,
            'date' => fake()->dateTimeBetween('now', '+6 months'),
            'location' => fake()->city(),
            'target_time' => fake()->numberBetween(1800, 14400), // 30min a 4h
            'actual_time' => null,
            'position' => null,
            'status' => 'upcoming',
        ];
    }

    /**
     * Indicate that the race is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'date' => fake()->dateTimeBetween('-6 months', 'now'),
            'actual_time' => $attributes['target_time'] + fake()->numberBetween(-600, 600),
            'position' => fake()->numberBetween(1, 500),
        ]);
    }

    /**
     * Indicate that the race is upcoming.
     */
    public function upcoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'upcoming',
            'date' => fake()->dateTimeBetween('now', '+6 months'),
        ]);
    }
}

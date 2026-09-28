<?php

namespace Database\Factories;

use App\Enums\AppointmentStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MedicalAppointment>
 */
class MedicalAppointmentFactory extends Factory
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
            'doctor_id' => null,
            'referred_from_id' => null,
            'scheduled_at' => $this->faker->dateTimeBetween('+1 day', '+2 months'),
            'location' => $this->faker->optional()->address(),
            'reason' => $this->faker->optional()->sentence(4),
            'status' => AppointmentStatus::Scheduled,
            'observations' => null,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'scheduled_at' => $this->faker->dateTimeBetween('-6 months', '-1 day'),
            'status' => AppointmentStatus::Completed,
            'observations' => $this->faker->paragraph(),
        ]);
    }

    public function awaitingFollowUp(): static
    {
        return $this->state(fn (array $attributes) => [
            'scheduled_at' => $this->faker->dateTimeBetween('-2 weeks', '-1 day'),
            'status' => AppointmentStatus::Scheduled,
        ]);
    }
}

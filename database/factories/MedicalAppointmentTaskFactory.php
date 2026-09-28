<?php

namespace Database\Factories;

use App\Enums\AppointmentTaskType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MedicalAppointmentTask>
 */
class MedicalAppointmentTaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'medical_appointment_id' => \App\Models\MedicalAppointment::factory(),
            'follow_up_appointment_id' => null,
            'type' => $this->faker->randomElement(AppointmentTaskType::cases()),
            'description' => $this->faker->sentence(5),
            'due_date' => $this->faker->optional()->dateTimeBetween('now', '+3 months'),
            'completed_at' => null,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'completed_at' => now(),
        ]);
    }
}

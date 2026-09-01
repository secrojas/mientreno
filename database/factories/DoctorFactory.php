<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Doctor>
 */
class DoctorFactory extends Factory
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
            'name' => 'Dr. '.$this->faker->lastName(),
            'specialty' => $this->faker->randomElement(['Cardiología', 'Clínica Médica', 'Traumatología', 'Nutrición']),
            'phone' => $this->faker->optional()->phoneNumber(),
            'email' => $this->faker->optional()->safeEmail(),
            'address' => $this->faker->optional()->address(),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}

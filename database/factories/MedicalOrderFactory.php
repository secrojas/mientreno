<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MedicalOrder>
 */
class MedicalOrderFactory extends Factory
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
            'title' => $this->faker->sentence(3),
            'notes' => $this->faker->optional()->sentence(),
            'file_path' => 'medical-orders/1/fake.jpg',
            'original_name' => $this->faker->word().'.jpg',
            'issued_at' => $this->faker->optional()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}

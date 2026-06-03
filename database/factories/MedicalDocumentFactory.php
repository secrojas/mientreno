<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MedicalDocument>
 */
class MedicalDocumentFactory extends Factory
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
            'type' => $this->faker->randomElement(\App\Enums\MedicalDocumentType::cases())->value,
            'title' => $this->faker->sentence(3),
            'notes' => $this->faker->optional()->sentence(),
            'file_path' => 'medical/1/fake.pdf',
            'original_name' => $this->faker->word().'.pdf',
            'issued_at' => $this->faker->optional()->dateTimeBetween('-2 years', 'now'),
            'expires_at' => null,
        ];
    }
}

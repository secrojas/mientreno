<?php

namespace Database\Factories;

use App\Enums\ShoeUsage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shoe>
 */
class ShoeFactory extends Factory
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
            'brand' => $this->faker->randomElement(['Nike', 'ASICS', 'Brooks', 'Saucony', 'Hoka', 'Adidas']),
            'model' => $this->faker->randomElement(['Pegasus 41', 'Novablast 5', 'Ghost 16', 'Ride 17', 'Clifton 9', 'Adizero SL2']),
            'nickname' => null,
            'color' => $this->faker->randomElement(['#2DE38E', '#FF3B5C', '#60A5FA', '#F59E0B', '#F9FAFB']),
            'photo_path' => null,
            'usage' => ShoeUsage::Daily,
            'purchased_at' => $this->faker->dateTimeBetween('-1 year', '-1 week'),
            'price' => null,
            'initial_km' => 0,
            'max_km' => 700,
            'is_default' => false,
            'retired_at' => null,
            'notes' => null,
        ];
    }

    public function retired(): static
    {
        return $this->state(fn (array $attributes) => [
            'retired_at' => now(),
        ]);
    }

    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
        ]);
    }
}

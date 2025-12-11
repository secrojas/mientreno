<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a test athlete user
        $user = User::factory()->create([
            'name' => 'Juan Pérez',
            'email' => 'atleta@test.com',
            'role' => 'athlete',
            'business_id' => null, // Individual user, not linked to any business yet
        ]);

        $this->command->info("✅ Usuario creado: {$user->name} ({$user->email})");

        // Seed workouts for this user
        $this->call(WorkoutSeeder::class);
    }
}

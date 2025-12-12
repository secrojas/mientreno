<?php

namespace Database\Seeders;

use App\Models\Race;
use App\Models\User;
use Illuminate\Database\Seeder;

class RaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', 'atleta@test.com')->first();

        if (!$user) {
            return;
        }

        // Carreras próximas
        Race::create([
            'user_id' => $user->id,
            'name' => '10K Buenos Aires',
            'distance' => 10,
            'date' => now()->addDays(15),
            'location' => 'Buenos Aires, Argentina',
            'target_time' => 2700, // 45 minutos
            'status' => 'upcoming',
            'notes' => 'Primera carrera del año, objetivo personal.',
        ]);

        Race::create([
            'user_id' => $user->id,
            'name' => 'Media Maratón de Palermo',
            'distance' => 21.1,
            'date' => now()->addDays(45),
            'location' => 'Parque de Palermo, CABA',
            'target_time' => 6300, // 1h 45min
            'status' => 'upcoming',
            'notes' => 'Preparación para la maratón.',
        ]);

        // Carreras pasadas
        Race::create([
            'user_id' => $user->id,
            'name' => '5K Running Club',
            'distance' => 5,
            'date' => now()->subDays(30),
            'location' => 'Parque Centenario',
            'target_time' => 1200, // 20 minutos
            'actual_time' => 1230, // 20:30
            'position' => 45,
            'status' => 'completed',
            'notes' => 'Buen ritmo, faltó un poco de velocidad en el final.',
        ]);

        Race::create([
            'user_id' => $user->id,
            'name' => 'Maratón de Buenos Aires',
            'distance' => 42.2,
            'date' => now()->subMonths(3),
            'location' => 'Buenos Aires, Argentina',
            'target_time' => 14400, // 4 horas
            'actual_time' => 14640, // 4h 4min
            'position' => 892,
            'status' => 'completed',
            'notes' => 'Primera maratón! Excelente experiencia, mejorable.',
        ]);

        Race::create([
            'user_id' => $user->id,
            'name' => '15K Costanera',
            'distance' => 15,
            'date' => now()->subMonths(2),
            'location' => 'Costanera Sur',
            'target_time' => 4500, // 1h 15min
            'actual_time' => 4380, // 1h 13min
            'position' => 125,
            'status' => 'completed',
            'notes' => 'Superé el objetivo! Buen pace constante.',
        ]);
    }
}

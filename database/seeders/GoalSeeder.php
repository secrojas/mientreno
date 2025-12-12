<?php

namespace Database\Seeders;

use App\Models\Goal;
use App\Models\User;
use App\Models\Race;
use Illuminate\Database\Seeder;

class GoalSeeder extends Seeder
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

        // Obtener la próxima carrera de 10K para vincular
        $race10K = $user->races()->where('distance', 10)->where('status', 'upcoming')->first();

        // Objetivo 1: Completar 10K en 45 minutos (vinculado a carrera)
        Goal::create([
            'user_id' => $user->id,
            'race_id' => $race10K?->id,
            'type' => 'race',
            'title' => 'Correr 10K sub 45 minutos',
            'description' => 'Objetivo para la carrera del mes que viene. Entrenar pace 4:30/km.',
            'target_value' => ['time' => 2700], // 45 minutos
            'target_date' => now()->addDays(15),
            'start_date' => now()->subDays(30),
            'status' => 'active',
            'progress' => ['current_value' => 2820, 'percentage' => 85], // Ya corrió en 47 min
        ]);

        // Objetivo 2: Correr 50km por semana
        Goal::create([
            'user_id' => $user->id,
            'type' => 'distance',
            'title' => 'Correr 50 km por semana',
            'description' => 'Aumentar volumen semanal progresivamente.',
            'target_value' => ['distance' => 50, 'period' => 'week'],
            'target_date' => now()->addMonths(2),
            'start_date' => now()->subWeek(),
            'status' => 'active',
            'progress' => ['current_value' => 42.5, 'percentage' => 85],
        ]);

        // Objetivo 3: Mejorar pace promedio a 5:00/km
        Goal::create([
            'user_id' => $user->id,
            'type' => 'pace',
            'title' => 'Pace promedio de 5:00/km',
            'description' => 'Mejorar pace en entrenamientos de fondo.',
            'target_value' => ['pace' => 300], // 5:00/km en segundos
            'target_date' => now()->addMonth(),
            'start_date' => now()->subDays(15),
            'status' => 'active',
            'progress' => ['current_value' => 315, 'percentage' => 65], // Actualmente en 5:15/km
        ]);

        // Objetivo 4: Entrenar 4 veces por semana
        Goal::create([
            'user_id' => $user->id,
            'type' => 'frequency',
            'title' => 'Entrenar 4 veces por semana',
            'description' => 'Mantener constancia y disciplina.',
            'target_value' => ['sessions' => 4, 'period' => 'week'],
            'target_date' => now()->addMonths(3),
            'start_date' => now()->subWeeks(2),
            'status' => 'active',
            'progress' => ['current_value' => 3, 'percentage' => 75],
        ]);

        // Objetivo 5: Completado - Primera 10K
        Goal::create([
            'user_id' => $user->id,
            'type' => 'race',
            'title' => 'Completar primera 10K',
            'description' => 'Terminar mi primera carrera de 10 km.',
            'target_value' => ['time' => 3000], // 50 minutos
            'target_date' => now()->subMonths(2),
            'start_date' => now()->subMonths(4),
            'status' => 'completed',
            'progress' => ['current_value' => 2880, 'percentage' => 100], // Completó en 48 min
        ]);
    }
}

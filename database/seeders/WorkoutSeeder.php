<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Workout;
use Illuminate\Database\Seeder;

class WorkoutSeeder extends Seeder
{
    public function run(): void
    {
        // Get the first user (assuming there's at least one user in the database)
        $user = User::first();

        if (!$user) {
            $this->command->warn('No hay usuarios en la base de datos. Crea un usuario primero.');
            return;
        }

        $this->command->info("Creando workouts para: {$user->name}");

        // Array of realistic workout data spanning the last 4 weeks
        $workouts = [
            // Week 4 (oldest)
            [
                'date' => now()->subWeeks(3)->startOfWeek()->addDays(0), // Lunes
                'type' => 'easy_run',
                'distance' => 8.5,
                'duration' => 2700, // 45 minutos
                'difficulty' => 2,
                'notes' => 'Trote suave de recuperación. Me sentí bien, piernas frescas.',
                'avg_heart_rate' => 145,
                'elevation_gain' => 50,
            ],
            [
                'date' => now()->subWeeks(3)->startOfWeek()->addDays(2), // Miércoles
                'type' => 'intervals',
                'distance' => 10.0,
                'duration' => 2700, // 45 minutos
                'difficulty' => 4,
                'notes' => '8x400m con 90" recuperación. Ritmos entre 3:45-3:50. Muy exigente.',
                'avg_heart_rate' => 175,
                'elevation_gain' => 30,
            ],
            [
                'date' => now()->subWeeks(3)->startOfWeek()->addDays(5), // Sábado
                'type' => 'long_run',
                'distance' => 18.0,
                'duration' => 5400, // 1h 30min
                'difficulty' => 3,
                'notes' => 'Rodaje largo por el parque. Buen ritmo sostenido.',
                'avg_heart_rate' => 155,
                'elevation_gain' => 120,
            ],

            // Week 3
            [
                'date' => now()->subWeeks(2)->startOfWeek()->addDays(0), // Lunes
                'type' => 'recovery',
                'distance' => 6.0,
                'duration' => 2100, // 35 minutos
                'difficulty' => 1,
                'notes' => 'Recuperación post long run. Muy suave.',
                'avg_heart_rate' => 135,
                'elevation_gain' => 20,
            ],
            [
                'date' => now()->subWeeks(2)->startOfWeek()->addDays(2), // Miércoles
                'type' => 'tempo',
                'distance' => 12.0,
                'duration' => 3300, // 55 minutos
                'difficulty' => 4,
                'notes' => '3km entrada + 6km tempo a 4:20 + 3km vuelta. Duro pero bien ejecutado.',
                'avg_heart_rate' => 168,
                'elevation_gain' => 80,
            ],
            [
                'date' => now()->subWeeks(2)->startOfWeek()->addDays(4), // Viernes
                'type' => 'easy_run',
                'distance' => 9.0,
                'duration' => 2880, // 48 minutos
                'difficulty' => 2,
                'notes' => 'Trote regenerativo. Clima perfecto, 18°C.',
                'avg_heart_rate' => 148,
                'elevation_gain' => 45,
            ],
            [
                'date' => now()->subWeeks(2)->startOfWeek()->addDays(6), // Domingo
                'type' => 'long_run',
                'distance' => 21.0,
                'duration' => 6300, // 1h 45min
                'difficulty' => 4,
                'notes' => 'Long run con progresión en los últimos 5km. Piernas pesadas al final.',
                'avg_heart_rate' => 160,
                'elevation_gain' => 180,
            ],

            // Week 2
            [
                'date' => now()->subWeeks(1)->startOfWeek()->addDays(1), // Martes
                'type' => 'intervals',
                'distance' => 11.0,
                'duration' => 3000, // 50 minutos
                'difficulty' => 5,
                'notes' => '5x1000m a ritmo 10k. Intervalos muy exigentes, última serie costó.',
                'avg_heart_rate' => 178,
                'elevation_gain' => 40,
            ],
            [
                'date' => now()->subWeeks(1)->startOfWeek()->addDays(3), // Jueves
                'type' => 'easy_run',
                'distance' => 7.5,
                'duration' => 2400, // 40 minutos
                'difficulty' => 2,
                'notes' => 'Trote suave entre series. Algo de agujetas en cuádriceps.',
                'avg_heart_rate' => 142,
                'elevation_gain' => 35,
            ],
            [
                'date' => now()->subWeeks(1)->startOfWeek()->addDays(6), // Domingo
                'type' => 'long_run',
                'distance' => 16.0,
                'duration' => 4800, // 1h 20min
                'difficulty' => 3,
                'notes' => 'Rodaje aeróbico continuo. Me sentí fuerte todo el recorrido.',
                'avg_heart_rate' => 152,
                'elevation_gain' => 110,
            ],

            // Week 1 (current week)
            [
                'date' => now()->startOfWeek()->addDays(0), // Lunes
                'type' => 'recovery',
                'distance' => 5.0,
                'duration' => 1800, // 30 minutos
                'difficulty' => 1,
                'notes' => 'Recuperación activa muy suave.',
                'avg_heart_rate' => 138,
                'elevation_gain' => 15,
            ],
            [
                'date' => now()->startOfWeek()->addDays(2), // Miércoles
                'type' => 'tempo',
                'distance' => 10.5,
                'duration' => 2880, // 48 minutos
                'difficulty' => 4,
                'notes' => 'Tempo run a 4:30/km. Buena sensación, controlado todo el tiempo.',
                'avg_heart_rate' => 165,
                'elevation_gain' => 60,
            ],
            [
                'date' => now()->startOfWeek()->addDays(4), // Viernes
                'type' => 'easy_run',
                'distance' => 8.0,
                'duration' => 2640, // 44 minutos
                'difficulty' => 2,
                'notes' => 'Trote regenerativo antes del long run del fin de semana.',
                'avg_heart_rate' => 146,
                'elevation_gain' => 40,
            ],
        ];

        foreach ($workouts as $workoutData) {
            // Calculate pace automatically
            $workoutData['avg_pace'] = Workout::calculatePace(
                $workoutData['distance'],
                $workoutData['duration']
            );
            $workoutData['user_id'] = $user->id;

            Workout::create($workoutData);
        }

        $this->command->info('✅ ' . count($workouts) . ' workouts creados exitosamente para ' . $user->name);
        $this->command->line('');
        $this->command->info('Resumen:');
        $this->command->line('- Total distancia: ' . number_format(array_sum(array_column($workouts, 'distance')), 1) . ' km');
        $this->command->line('- Total duración: ' . gmdate('H:i:s', array_sum(array_column($workouts, 'duration'))));
    }
}

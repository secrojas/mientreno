<?php

namespace App\Console\Commands;

use App\Models\Workout;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PDO;

class ImportWorkoutsFromOldDb extends Command
{
    protected $signature = 'workouts:import-from-old-db
                            {--user-id=2 : ID del usuario en la BD nueva}
                            {--old-user-id=730 : ID del usuario en la BD antigua}
                            {--dry-run : Previsualizar sin insertar datos}
                            {--force : Sobrescribir duplicados existentes}';

    protected $description = 'Importa workouts desde la BD antigua running-api a la BD actual';

    private PDO $oldDbConnection;
    private array $stats = [
        'total' => 0,
        'imported' => 0,
        'skipped' => 0,
        'duplicates' => 0,
        'errors' => 0,
    ];

    public function handle()
    {
        $userId = (int) $this->option('user-id');
        $oldUserId = (int) $this->option('old-user-id');
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        $this->info('=== IMPORTACIÓN DE WORKOUTS DESDE BD ANTIGUA ===');
        $this->newLine();

        // Verificar usuario destino
        $user = DB::table('users')->find($userId);
        if (!$user) {
            $this->error("❌ Usuario con ID {$userId} no existe en la BD actual.");
            return 1;
        }

        $this->info("👤 Usuario destino: {$user->name} (ID: {$userId})");
        $this->info("📦 Usuario origen: ID {$oldUserId} en running-api");
        $this->newLine();

        if ($dryRun) {
            $this->warn('🔍 MODO DRY-RUN: No se insertarán datos reales');
            $this->newLine();
        }

        // Conectar a BD antigua
        try {
            $this->connectToOldDb();
        } catch (\Exception $e) {
            $this->error("❌ Error conectando a BD antigua: {$e->getMessage()}");
            return 1;
        }

        // Obtener workouts antiguos
        $this->info('📊 Obteniendo workouts de BD antigua...');
        $oldWorkouts = $this->fetchOldWorkouts($oldUserId);
        $this->stats['total'] = count($oldWorkouts);

        if ($this->stats['total'] === 0) {
            $this->warn("⚠️  No se encontraron workouts para el usuario {$oldUserId}");
            return 0;
        }

        $this->info("✓ Encontrados {$this->stats['total']} workouts");
        $this->newLine();

        // Procesar cada workout
        $progressBar = $this->output->createProgressBar($this->stats['total']);
        $progressBar->start();

        foreach ($oldWorkouts as $oldWorkout) {
            $this->processWorkout($oldWorkout, $userId, $dryRun, $force);
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Mostrar resumen
        $this->displaySummary($dryRun);

        return 0;
    }

    private function connectToOldDb(): void
    {
        $config = config('database.connections.mysql');
        $dsn = "mysql:host={$config['host']};dbname=running-api;charset=utf8mb4";

        $this->oldDbConnection = new PDO(
            $dsn,
            $config['username'],
            $config['password'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }

    private function fetchOldWorkouts(int $oldUserId): array
    {
        $stmt = $this->oldDbConnection->prepare('
            SELECT * FROM workouts
            WHERE user_id = :user_id
            AND status = "completed"
            ORDER BY date ASC
        ');

        $stmt->execute(['user_id' => $oldUserId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function processWorkout(array $oldWorkout, int $userId, bool $dryRun, bool $force): void
    {
        try {
            // Verificar duplicado
            $exists = Workout::where('user_id', $userId)
                ->where('date', $oldWorkout['date'])
                ->exists();

            if ($exists && !$force) {
                $this->stats['duplicates']++;
                return;
            }

            // Transformar datos
            $newWorkout = $this->transformWorkout($oldWorkout, $userId);

            if (!$dryRun) {
                if ($exists && $force) {
                    // Actualizar existente
                    Workout::where('user_id', $userId)
                        ->where('date', $oldWorkout['date'])
                        ->delete();
                }

                Workout::create($newWorkout);
                $this->stats['imported']++;
            } else {
                $this->stats['imported']++;
            }
        } catch (\Exception $e) {
            $this->stats['errors']++;
            $this->newLine();
            $this->error("❌ Error procesando workout {$oldWorkout['date']}: {$e->getMessage()}");
        }
    }

    private function transformWorkout(array $old, int $userId): array
    {
        $distance = (float) str_replace(',', '.', $old['distance_km']);
        $duration = $this->convertTimeToSeconds($old['duration']);

        return [
            'user_id' => $userId,
            'date' => $old['date'],
            'distance' => $distance,
            'duration' => $duration,
            'avg_pace' => Workout::calculatePace($distance, $duration),
            'type' => $this->mapTrainingType($old['training_type_id']),
            'difficulty' => $this->mapDifficulty($old['difficulty']),
            'avg_heart_rate' => $old['heart_rate_avg'] ?? null,
            'is_race' => $old['training_type_id'] == 3,
            'notes' => $this->combineNotes($old['title'], $old['description']),
            'training_group_id' => null,
            'race_id' => null,
            'elevation_gain' => null,
            'weather' => null,
            'route' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private function convertTimeToSeconds(string $time): int
    {
        // Formato: HH:MM:SS
        $parts = explode(':', $time);
        $hours = (int) $parts[0];
        $minutes = (int) $parts[1];
        $seconds = (int) $parts[2];

        return ($hours * 3600) + ($minutes * 60) + $seconds;
    }

    private function mapTrainingType(int $trainingTypeId): string
    {
        return match ($trainingTypeId) {
            1 => 'training_run',  // Entrenamiento
            2 => 'easy_run',      // Fondo
            3 => 'race',          // Carrera
            default => 'training_run',
        };
    }

    private function mapDifficulty(?string $difficulty): int
    {
        return match ($difficulty) {
            'easy' => 2,
            'moderate' => 3,
            'hard' => 4,
            default => 3,
        };
    }

    private function combineNotes(?string $title, ?string $description): ?string
    {
        $parts = array_filter([$title, $description]);

        if (empty($parts)) {
            return null;
        }

        if (count($parts) === 1) {
            return $parts[0];
        }

        // Si title y description son iguales, solo retornar uno
        if ($title === $description) {
            return $title;
        }

        return implode("\n\n", $parts);
    }

    private function displaySummary(bool $dryRun): void
    {
        $this->info('=== RESUMEN DE IMPORTACIÓN ===');
        $this->newLine();

        $this->table(
            ['Métrica', 'Cantidad'],
            [
                ['Total procesados', $this->stats['total']],
                ['✓ Importados', $this->stats['imported']],
                ['⊘ Duplicados (omitidos)', $this->stats['duplicates']],
                ['✗ Errores', $this->stats['errors']],
            ]
        );

        if ($dryRun) {
            $this->newLine();
            $this->warn('⚠️  Esto fue una previsualización (dry-run)');
            $this->info('Para importar realmente, ejecuta el comando sin --dry-run');
        } else {
            $this->newLine();
            $this->info('✅ Importación completada exitosamente');
        }

        if ($this->stats['duplicates'] > 0 && !$dryRun) {
            $this->newLine();
            $this->comment('💡 Hay duplicados omitidos. Usa --force para sobrescribirlos');
        }
    }
}

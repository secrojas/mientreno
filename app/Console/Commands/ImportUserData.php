<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Workout;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ImportUserData extends Command
{
    protected $signature = 'user:import
                            {file? : Ruta del archivo JSON a importar (por defecto: storage/app/user-export.json)}
                            {--business-id= : ID del business al que pertenecerá el usuario}
                            {--dry-run : Previsualizar sin insertar datos}
                            {--force : Sobrescribir workouts duplicados existentes}
                            {--skip-user : Omitir creación de usuario (solo importar workouts)}';

    protected $description = 'Importa un usuario y sus workouts desde un archivo JSON';

    private array $stats = [
        'user_created' => false,
        'user_existed' => false,
        'workouts_total' => 0,
        'workouts_imported' => 0,
        'workouts_duplicates' => 0,
        'workouts_errors' => 0,
    ];

    public function handle()
    {
        $filePath = $this->argument('file') ?? 'user-export.json';
        $businessId = $this->option('business-id');
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');
        $skipUser = $this->option('skip-user');

        $this->info('=== IMPORTACIÓN DE USUARIO Y WORKOUTS ===');
        $this->newLine();

        if ($dryRun) {
            $this->warn('🔍 MODO DRY-RUN: No se insertarán datos reales');
            $this->newLine();
        }

        // Leer archivo
        try {
            if (!Storage::disk('local')->exists($filePath)) {
                $this->error("❌ Archivo no encontrado: {$filePath}");
                $this->line("   Ruta esperada: " . Storage::disk('local')->path($filePath));
                return 1;
            }

            $json = Storage::disk('local')->get($filePath);
            $data = json_decode($json, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->error("❌ Error al leer JSON: " . json_last_error_msg());
                return 1;
            }

            $this->info("✓ Archivo cargado correctamente");
            $this->info("  Exportado el: {$data['exported_at']}");
            $this->newLine();

        } catch (\Exception $e) {
            $this->error("❌ Error al leer archivo: {$e->getMessage()}");
            return 1;
        }

        // Mostrar info del usuario
        $this->line("👤 Usuario a importar:");
        $this->line("   Nombre: {$data['user']['name']}");
        $this->line("   Email: {$data['user']['email']}");
        $this->line("   Workouts: {$data['stats']['total_workouts']}");
        $this->newLine();

        // Procesar usuario
        $userId = null;

        if (!$skipUser) {
            $userId = $this->processUser($data['user'], $businessId, $dryRun);

            if (!$userId) {
                return 1;
            }
        } else {
            // Si se omite usuario, buscar por email
            $existingUser = User::where('email', $data['user']['email'])->first();

            if (!$existingUser) {
                $this->error("❌ No se puede omitir usuario porque no existe en la BD.");
                $this->line("   Ejecuta sin --skip-user para crear el usuario.");
                return 1;
            }

            $userId = $existingUser->id;
            $this->info("✓ Usando usuario existente: {$existingUser->name} (ID: {$userId})");
            $this->newLine();
        }

        // Procesar workouts
        $this->stats['workouts_total'] = count($data['workouts']);

        if ($this->stats['workouts_total'] > 0) {
            $this->info("📊 Procesando {$this->stats['workouts_total']} workouts...");
            $this->newLine();

            $progressBar = $this->output->createProgressBar($this->stats['workouts_total']);
            $progressBar->start();

            foreach ($data['workouts'] as $workoutData) {
                $this->processWorkout($workoutData, $userId, $dryRun, $force);
                $progressBar->advance();
            }

            $progressBar->finish();
            $this->newLine(2);
        }

        // Mostrar resumen
        $this->displaySummary($dryRun);

        return 0;
    }

    private function processUser(array $userData, ?int $businessId, bool $dryRun): ?int
    {
        // Verificar si ya existe
        $existingUser = User::where('email', $userData['email'])->first();

        if ($existingUser) {
            $this->stats['user_existed'] = true;
            $this->warn("⚠️  El usuario ya existe: {$existingUser->name} (ID: {$existingUser->id})");
            $this->line("   Se omitirá la creación del usuario y se usará el existente para los workouts.");
            $this->newLine();

            return $existingUser->id;
        }

        // Preparar datos
        $userToCreate = [
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => $userData['password'], // Ya hasheada
            'business_id' => $businessId,
            'role' => $userData['role'] ?? 'runner',
            'avatar' => $userData['avatar'],
            'birth_date' => $userData['birth_date'],
            'gender' => $userData['gender'],
            'weight' => $userData['weight'],
            'height' => $userData['height'],
            'bio' => $userData['bio'],
            'email_verified_at' => $userData['email_verified_at'],
        ];

        if (!$dryRun) {
            try {
                $newUser = User::create($userToCreate);
                $this->stats['user_created'] = true;
                $this->info("✓ Usuario creado: {$newUser->name} (ID: {$newUser->id})");
                $this->newLine();

                return $newUser->id;

            } catch (\Exception $e) {
                $this->error("❌ Error creando usuario: {$e->getMessage()}");
                return null;
            }
        }

        $this->stats['user_created'] = true;
        return 1; // ID ficticio para dry-run
    }

    private function processWorkout(array $workoutData, int $userId, bool $dryRun, bool $force): void
    {
        try {
            // Verificar duplicado por fecha
            $exists = Workout::where('user_id', $userId)
                ->where('date', $workoutData['date'])
                ->exists();

            if ($exists && !$force) {
                $this->stats['workouts_duplicates']++;
                return;
            }

            // Preparar datos
            $workoutToCreate = [
                'user_id' => $userId,
                'date' => $workoutData['date'],
                'type' => $workoutData['type'],
                'status' => $workoutData['status'],
                'distance' => $workoutData['distance'],
                'planned_distance' => $workoutData['planned_distance'],
                'duration' => $workoutData['duration'],
                'avg_pace' => $workoutData['avg_pace'],
                'avg_heart_rate' => $workoutData['avg_heart_rate'],
                'elevation_gain' => $workoutData['elevation_gain'],
                'difficulty' => $workoutData['difficulty'],
                'notes' => $workoutData['notes'],
                'skip_reason' => $workoutData['skip_reason'],
                'weather' => $workoutData['weather'],
                'route' => $workoutData['route'],
                'is_race' => $workoutData['is_race'],
                'training_group_id' => null,
                'race_id' => null,
            ];

            if (!$dryRun) {
                if ($exists && $force) {
                    // Eliminar existente
                    Workout::where('user_id', $userId)
                        ->where('date', $workoutData['date'])
                        ->delete();
                }

                Workout::create($workoutToCreate);
                $this->stats['workouts_imported']++;
            } else {
                $this->stats['workouts_imported']++;
            }

        } catch (\Exception $e) {
            $this->stats['workouts_errors']++;
        }
    }

    private function displaySummary(bool $dryRun): void
    {
        $this->info('=== RESUMEN DE IMPORTACIÓN ===');
        $this->newLine();

        // Resumen de usuario
        $this->line('USUARIO:');
        if ($this->stats['user_created']) {
            $this->line('  ✓ Usuario creado');
        } elseif ($this->stats['user_existed']) {
            $this->line('  → Usuario ya existía (se usó el existente)');
        }
        $this->newLine();

        // Resumen de workouts
        $this->line('WORKOUTS:');
        $this->table(
            ['Métrica', 'Cantidad'],
            [
                ['Total en archivo', $this->stats['workouts_total']],
                ['✓ Importados', $this->stats['workouts_imported']],
                ['⊘ Duplicados (omitidos)', $this->stats['workouts_duplicates']],
                ['✗ Errores', $this->stats['workouts_errors']],
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

        if ($this->stats['workouts_duplicates'] > 0 && !$dryRun) {
            $this->newLine();
            $this->comment('💡 Hay duplicados omitidos. Usa --force para sobrescribirlos');
        }
    }
}

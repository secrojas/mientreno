<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Workout;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ExportUserData extends Command
{
    protected $signature = 'user:export
                            {email : Email del usuario a exportar}
                            {--output= : Ruta del archivo de salida (por defecto: storage/app/user-export.json)}';

    protected $description = 'Exporta un usuario y todos sus workouts a un archivo JSON';

    public function handle()
    {
        $email = $this->argument('email');
        $outputPath = $this->option('output') ?? 'user-export.json';

        $this->info('=== EXPORTACIÓN DE USUARIO Y WORKOUTS ===');
        $this->newLine();

        // Buscar usuario
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("❌ Usuario con email '{$email}' no encontrado.");
            return 1;
        }

        $this->info("👤 Usuario encontrado: {$user->name} (ID: {$user->id})");
        $this->newLine();

        // Obtener workouts
        $workouts = Workout::where('user_id', $user->id)->get();
        $workoutsCount = $workouts->count();

        $this->info("📊 Workouts encontrados: {$workoutsCount}");
        $this->newLine();

        // Preparar datos del usuario (excluyendo campos sensibles)
        $userData = [
            'name' => $user->name,
            'email' => $user->email,
            'password' => $user->password, // Ya hasheada
            'role' => $user->role,
            'profile' => $user->profile,
            'avatar' => $user->avatar,
            'birth_date' => $user->birth_date?->format('Y-m-d'),
            'gender' => $user->gender,
            'weight' => $user->weight,
            'height' => $user->height,
            'bio' => $user->bio,
            'email_verified_at' => $user->email_verified_at?->format('Y-m-d H:i:s'),
        ];

        // Preparar datos de workouts
        $workoutsData = $workouts->map(function ($workout) {
            return [
                'date' => $workout->date->format('Y-m-d'),
                'type' => $workout->type,
                'status' => $workout->status,
                'distance' => $workout->distance,
                'planned_distance' => $workout->planned_distance,
                'duration' => $workout->duration,
                'avg_pace' => $workout->avg_pace,
                'avg_heart_rate' => $workout->avg_heart_rate,
                'elevation_gain' => $workout->elevation_gain,
                'difficulty' => $workout->difficulty,
                'notes' => $workout->notes,
                'skip_reason' => $workout->skip_reason,
                'weather' => $workout->weather,
                'route' => $workout->route,
                'is_race' => $workout->is_race,
            ];
        })->toArray();

        // Preparar estructura final
        $exportData = [
            'exported_at' => now()->format('Y-m-d H:i:s'),
            'user' => $userData,
            'workouts' => $workoutsData,
            'stats' => [
                'total_workouts' => $workoutsCount,
            ],
        ];

        // Guardar en archivo
        try {
            Storage::disk('local')->put($outputPath, json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            $fullPath = Storage::disk('local')->path($outputPath);

            $this->newLine();
            $this->info('✅ Exportación completada exitosamente');
            $this->newLine();
            $this->line("📁 Archivo guardado en: {$fullPath}");
            $this->newLine();

            // Mostrar tabla resumen
            $this->table(
                ['Concepto', 'Valor'],
                [
                    ['Usuario', $user->name],
                    ['Email', $user->email],
                    ['Total workouts', $workoutsCount],
                    ['Tamaño archivo', $this->formatBytes(Storage::disk('local')->size($outputPath))],
                ]
            );

            if ($user->avatar) {
                $this->newLine();
                $this->warn("⚠️  IMPORTANTE: El usuario tiene un avatar: {$user->avatar}");
                $this->warn("   Debes copiar manualmente el archivo de storage/app/public/{$user->avatar}");
                $this->warn("   a producción en la misma ruta.");
            }

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Error al guardar archivo: {$e->getMessage()}");
            return 1;
        }
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' bytes';
    }
}

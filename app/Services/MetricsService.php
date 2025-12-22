<?php

namespace App\Services;

use App\Models\User;
use App\Models\Workout;
use Illuminate\Support\Carbon;

class MetricsService
{
    /**
     * Obtener métricas semanales de un usuario
     * Solo cuenta workouts completados (excluye planned y skipped)
     */
    public function getWeeklyMetrics(User $user): array
    {
        $workouts = $user->workouts()->thisWeek()->completed()->get();

        return [
            'total_distance' => $workouts->sum('distance'),
            'total_duration' => $workouts->sum('duration'),
            'total_workouts' => $workouts->count(),
            'avg_pace' => $workouts->avg('avg_pace'),
        ];
    }

    /**
     * Obtener métricas mensuales de un usuario
     * Solo cuenta workouts completados (excluye planned y skipped)
     */
    public function getMonthlyMetrics(User $user): array
    {
        $workouts = $user->workouts()->thisMonth()->completed()->get();

        return [
            'total_distance' => $workouts->sum('distance'),
            'total_duration' => $workouts->sum('duration'),
            'total_workouts' => $workouts->count(),
            'avg_pace' => $workouts->avg('avg_pace'),
        ];
    }

    /**
     * Obtener métricas anuales de un usuario
     * Solo cuenta workouts completados (excluye planned y skipped)
     */
    public function getYearlyMetrics(User $user): array
    {
        $workouts = $user->workouts()->thisYear()->completed()->get();

        return [
            'total_distance' => $workouts->sum('distance'),
            'total_duration' => $workouts->sum('duration'),
            'total_workouts' => $workouts->count(),
            'avg_pace' => $workouts->avg('avg_pace'),
        ];
    }

    /**
     * Obtener métricas totales de un usuario
     * Solo cuenta workouts completados (excluye planned y skipped)
     */
    public function getTotalMetrics(User $user): array
    {
        return [
            'total_distance' => $user->workouts()->completed()->sum('distance'),
            'total_duration' => $user->workouts()->completed()->sum('duration'),
            'total_workouts' => $user->workouts()->completed()->count(),
            'avg_pace' => $user->workouts()->completed()->avg('avg_pace'),
        ];
    }

    /**
     * Formatear duración de segundos a formato legible
     */
    public function formatDuration(int $seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }

        return "{$minutes}m";
    }

    /**
     * Formatear pace de segundos/km a formato MM:SS
     */
    public function formatPace(?int $paceInSeconds): string
    {
        if (!$paceInSeconds) {
            return '–';
        }

        $minutes = floor($paceInSeconds / 60);
        $seconds = $paceInSeconds % 60;

        return sprintf("%d:%02d", $minutes, $seconds);
    }

    /**
     * Obtener distribución de tipos de entrenamientos
     * Solo cuenta workouts completados (excluye planned y skipped)
     */
    public function getWorkoutTypeDistribution(User $user): array
    {
        return $user->workouts()
            ->completed()
            ->selectRaw('type, COUNT(*) as count, SUM(distance) as total_distance')
            ->groupBy('type')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->type => [
                    'count' => $item->count,
                    'total_distance' => $item->total_distance,
                ]];
            })
            ->toArray();
    }

    /**
     * Calcular racha de entrenamientos (días consecutivos)
     * Solo cuenta workouts completados (excluye planned y skipped)
     */
    public function calculateStreak(User $user): int
    {
        $workouts = $user->workouts()
            ->completed()
            ->orderBy('date', 'desc')
            ->get()
            ->pluck('date')
            ->map(fn($date) => $date->format('Y-m-d'))
            ->unique();

        $streak = 0;
        $currentDate = Carbon::today();

        foreach ($workouts as $workoutDate) {
            $date = Carbon::parse($workoutDate);

            // Si la fecha del workout es hoy o ayer, continuar la racha
            if ($date->isSameDay($currentDate) || $date->isSameDay($currentDate->copy()->subDay())) {
                $streak++;
                $currentDate = $date->copy()->subDay();
            } else {
                break;
            }
        }

        return $streak;
    }

    /**
     * Obtener entrenamientos recientes del usuario
     */
    public function getRecentWorkouts(User $user, int $limit = 5)
    {
        return $user->workouts()
            ->orderBy('date', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Comparar métricas entre dos períodos
     * Solo cuenta workouts completados (excluye planned y skipped)
     */
    public function compareWeekToWeek(User $user): array
    {
        $thisWeek = $this->getWeeklyMetrics($user);

        // Semana anterior - solo completados
        $lastWeekWorkouts = $user->workouts()
            ->completed()
            ->whereBetween('date', [
                Carbon::now()->subWeek()->startOfWeek(),
                Carbon::now()->subWeek()->endOfWeek(),
            ])
            ->get();

        $lastWeek = [
            'total_distance' => $lastWeekWorkouts->sum('distance'),
            'total_workouts' => $lastWeekWorkouts->count(),
        ];

        return [
            'distance_diff' => $thisWeek['total_distance'] - $lastWeek['total_distance'],
            'workouts_diff' => $thisWeek['total_workouts'] - $lastWeek['total_workouts'],
            'distance_percent' => $lastWeek['total_distance'] > 0
                ? (($thisWeek['total_distance'] - $lastWeek['total_distance']) / $lastWeek['total_distance']) * 100
                : 0,
        ];
    }
}

<?php

namespace App\Services;

use App\Models\Goal;
use App\Models\User;
use Carbon\Carbon;

class GoalProgressService
{
    /**
     * Calcula el progreso de un goal basándose en los workouts del usuario
     */
    public function calculateProgress(Goal $goal): array
    {
        return match($goal->type) {
            'race' => $this->calculateRaceProgress($goal),
            'distance' => $this->calculateDistanceProgress($goal),
            'pace' => $this->calculatePaceProgress($goal),
            'frequency' => $this->calculateFrequencyProgress($goal),
            default => ['current_value' => 0, 'percentage' => 0],
        };
    }

    /**
     * Calcula el progreso de un goal de tipo "race"
     * Busca workouts vinculados a la carrera asociada
     */
    protected function calculateRaceProgress(Goal $goal): array
    {
        if (!$goal->race_id) {
            return ['current_value' => 0, 'percentage' => 0];
        }

        // Buscar workouts de tipo "race" vinculados a esta carrera
        $raceWorkout = $goal->user->workouts()
            ->where('race_id', $goal->race_id)
            ->where('type', 'race')
            ->orderBy('date', 'desc')
            ->first();

        if (!$raceWorkout) {
            return ['current_value' => 0, 'percentage' => 0];
        }

        $targetTime = $goal->target_value['time'] ?? 0;
        $actualTime = $raceWorkout->duration;

        // El progreso es mejor si el tiempo es menor (más rápido)
        // Si ya completó la carrera, calcular el porcentaje basado en el tiempo
        if ($actualTime <= $targetTime) {
            $percentage = 100;
        } else {
            // Si fue más lento, calcular cuánto se acercó
            $percentage = min(100, round(($targetTime / $actualTime) * 100));
        }

        return [
            'current_value' => $actualTime,
            'percentage' => $percentage,
        ];
    }

    /**
     * Calcula el progreso de un goal de tipo "distance"
     * Suma la distancia total en el período especificado
     */
    protected function calculateDistanceProgress(Goal $goal): array
    {
        $targetDistance = $goal->target_value['distance'] ?? 0;
        $period = $goal->target_value['period'] ?? 'week';

        // Determinar el rango de fechas según el período
        $startDate = $this->getStartDateForPeriod($period);
        $endDate = now();

        // Sumar distancia total en el período
        $currentDistance = $goal->user->workouts()
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('distance');

        $percentage = $targetDistance > 0
            ? min(100, round(($currentDistance / $targetDistance) * 100))
            : 0;

        return [
            'current_value' => round($currentDistance, 1),
            'percentage' => $percentage,
        ];
    }

    /**
     * Calcula el progreso de un goal de tipo "pace"
     * Calcula el pace promedio de los últimos workouts
     */
    protected function calculatePaceProgress(Goal $goal): array
    {
        $targetPace = $goal->target_value['pace'] ?? 0;

        // Obtener los últimos 5 workouts para calcular el pace promedio
        $recentWorkouts = $goal->user->workouts()
            ->whereNotNull('avg_pace')
            ->where('avg_pace', '>', 0)
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();

        if ($recentWorkouts->isEmpty()) {
            return ['current_value' => 0, 'percentage' => 0];
        }

        $currentPace = $recentWorkouts->avg('avg_pace');

        // El progreso es mejor si el pace es menor (más rápido)
        // Si el pace actual es igual o mejor que el objetivo
        if ($currentPace <= $targetPace) {
            $percentage = 100;
        } else {
            // Calcular cuánto se ha acercado al objetivo
            // Usar una escala: si está al 110% del objetivo = 50% de progreso
            $paceRatio = $currentPace / $targetPace;
            $percentage = max(0, min(100, round((2 - $paceRatio) * 100)));
        }

        return [
            'current_value' => round($currentPace),
            'percentage' => max(0, $percentage),
        ];
    }

    /**
     * Calcula el progreso de un goal de tipo "frequency"
     * Cuenta el número de sesiones en el período especificado
     */
    protected function calculateFrequencyProgress(Goal $goal): array
    {
        $targetSessions = $goal->target_value['sessions'] ?? 0;
        $period = $goal->target_value['period'] ?? 'week';

        // Determinar el rango de fechas según el período
        $startDate = $this->getStartDateForPeriod($period);
        $endDate = now();

        // Contar sesiones en el período
        $currentSessions = $goal->user->workouts()
            ->whereBetween('date', [$startDate, $endDate])
            ->count();

        $percentage = $targetSessions > 0
            ? min(100, round(($currentSessions / $targetSessions) * 100))
            : 0;

        return [
            'current_value' => $currentSessions,
            'percentage' => $percentage,
        ];
    }

    /**
     * Determina la fecha de inicio según el período
     */
    protected function getStartDateForPeriod(string $period): Carbon
    {
        return match($period) {
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => now()->startOfWeek(),
        };
    }

    /**
     * Actualiza el progreso de un goal específico
     */
    public function updateGoalProgress(Goal $goal): void
    {
        $progress = $this->calculateProgress($goal);

        $goal->update([
            'progress' => $progress,
        ]);
    }

    /**
     * Actualiza el progreso de todos los goals activos de un usuario
     */
    public function updateUserGoalsProgress(User $user): void
    {
        $activeGoals = $user->goals()->active()->get();

        foreach ($activeGoals as $goal) {
            $this->updateGoalProgress($goal);
        }
    }

    /**
     * Actualiza el progreso de todos los goals activos del sistema
     */
    public function updateAllGoalsProgress(): void
    {
        $activeGoals = Goal::where('status', 'active')->get();

        foreach ($activeGoals as $goal) {
            $this->updateGoalProgress($goal);
        }
    }
}

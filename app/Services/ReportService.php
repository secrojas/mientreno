<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ReportService
{
    protected MetricsService $metricsService;

    public function __construct(MetricsService $metricsService)
    {
        $this->metricsService = $metricsService;
    }

    /**
     * Obtener reporte semanal completo
     */
    public function getWeeklyReport(User $user, ?int $year = null, ?int $week = null): array
    {
        $year = $year ?? now()->year;
        $week = $week ?? now()->week;

        // Calcular fechas de inicio y fin de la semana (ISO 8601: Lunes a Domingo)
        $startDate = Carbon::now()->setISODate($year, $week)->startOfWeek();
        $endDate = $startDate->copy()->endOfWeek();

        // Obtener workouts del período
        $workouts = $user->workouts()
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'asc')
            ->get();

        // Calcular semana anterior para comparativa
        $prevWeek = $week - 1;
        $prevYear = $year;
        if ($prevWeek < 1) {
            $prevWeek = Carbon::now()->setISODate($year - 1, 52)->week;
            $prevYear = $year - 1;
        }

        $prevStartDate = Carbon::now()->setISODate($prevYear, $prevWeek)->startOfWeek();
        $prevEndDate = $prevStartDate->copy()->endOfWeek();

        $prevWorkouts = $user->workouts()
            ->whereBetween('date', [$prevStartDate, $prevEndDate])
            ->get();

        // Semana siguiente (para navegación)
        $nextWeek = $week + 1;
        $nextYear = $year;
        $lastWeekOfYear = Carbon::now()->setISODate($year, 52)->week;
        if ($nextWeek > $lastWeekOfYear) {
            $nextWeek = 1;
            $nextYear = $year + 1;
        }

        return [
            'period' => [
                'type' => 'weekly',
                'year' => $year,
                'week' => $week,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'label' => "Semana {$week}, {$year}",
                'prev_year' => $prevYear,
                'prev_week' => $prevWeek,
                'next_year' => $nextYear,
                'next_week' => $nextWeek,
                'is_current_week' => $year === now()->year && $week === now()->week,
            ],
            'summary' => $this->calculateSummary($workouts),
            'distribution' => $this->getWorkoutDistribution($workouts),
            'comparison' => $this->getComparison(
                $this->calculateSummary($workouts),
                $this->calculateSummary($prevWorkouts)
            ),
            'workouts' => $workouts,
            'insights' => $this->getInsights($workouts, $user),
        ];
    }

    /**
     * Obtener reporte mensual completo
     */
    public function getMonthlyReport(User $user, ?int $year = null, ?int $month = null): array
    {
        $year = $year ?? now()->year;
        $month = $month ?? now()->month;

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // Obtener workouts del período
        $workouts = $user->workouts()
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'asc')
            ->get();

        // Calcular mes anterior para comparativa
        $prevMonth = $month - 1;
        $prevYear = $year;
        if ($prevMonth < 1) {
            $prevMonth = 12;
            $prevYear = $year - 1;
        }

        $prevStartDate = Carbon::createFromDate($prevYear, $prevMonth, 1)->startOfMonth();
        $prevEndDate = $prevStartDate->copy()->endOfMonth();

        $prevWorkouts = $user->workouts()
            ->whereBetween('date', [$prevStartDate, $prevEndDate])
            ->get();

        // Mes siguiente (para navegación)
        $nextMonth = $month + 1;
        $nextYear = $year;
        if ($nextMonth > 12) {
            $nextMonth = 1;
            $nextYear = $year + 1;
        }

        $monthName = $startDate->locale('es')->monthName;

        return [
            'period' => [
                'type' => 'monthly',
                'year' => $year,
                'month' => $month,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'label' => ucfirst($monthName) . " {$year}",
                'prev_year' => $prevYear,
                'prev_month' => $prevMonth,
                'next_year' => $nextYear,
                'next_month' => $nextMonth,
                'is_current_month' => $year === now()->year && $month === now()->month,
            ],
            'summary' => $this->calculateSummary($workouts),
            'distribution' => $this->getWorkoutDistribution($workouts),
            'comparison' => $this->getComparison(
                $this->calculateSummary($workouts),
                $this->calculateSummary($prevWorkouts)
            ),
            'workouts' => $workouts,
            'insights' => $this->getInsights($workouts, $user),
        ];
    }

    /**
     * Calcular resumen de métricas
     * Solo cuenta workouts completados para métricas (excluye planned y skipped)
     */
    protected function calculateSummary(Collection $workouts): array
    {
        // Filtrar solo workouts completados para métricas
        $completedWorkouts = $workouts->where('status', 'completed');

        $totalDistance = $completedWorkouts->sum('distance');
        $totalDuration = $completedWorkouts->sum('duration');
        $totalSessions = $completedWorkouts->count();
        $avgPace = $completedWorkouts->avg('avg_pace');
        $avgHeartRate = $completedWorkouts->whereNotNull('avg_heart_rate')->avg('avg_heart_rate');
        $elevationGain = $completedWorkouts->sum('elevation_gain');

        return [
            'total_distance' => round($totalDistance, 2),
            'total_duration' => $totalDuration,
            'total_sessions' => $totalSessions,
            'avg_pace' => $avgPace ? round($avgPace) : null,
            'avg_heart_rate' => $avgHeartRate ? round($avgHeartRate) : null,
            'elevation_gain' => $elevationGain,
            'formatted_duration' => $this->metricsService->formatDuration($totalDuration),
            'formatted_pace' => $this->metricsService->formatPace($avgPace ? round($avgPace) : null),
        ];
    }

    /**
     * Obtener distribución de tipos de entrenamientos con porcentajes
     * Solo cuenta workouts completados (excluye planned y skipped)
     */
    public function getWorkoutDistribution(Collection $workouts): array
    {
        // Filtrar solo completados para distribución
        $completedWorkouts = $workouts->where('status', 'completed');

        if ($completedWorkouts->isEmpty()) {
            return [];
        }

        $totalDistance = $completedWorkouts->sum('distance');

        $distribution = $completedWorkouts->groupBy('type')->map(function ($group) use ($totalDistance) {
            $distance = $group->sum('distance');
            return [
                'count' => $group->count(),
                'distance' => round($distance, 2),
                'percentage' => $totalDistance > 0 ? round(($distance / $totalDistance) * 100, 1) : 0,
            ];
        })->toArray();

        return $distribution;
    }

    /**
     * Obtener comparativa entre dos períodos
     */
    public function getComparison(array $current, array $previous): array
    {
        return [
            'distance' => $this->calculateDiff(
                $current['total_distance'],
                $previous['total_distance']
            ),
            'duration' => $this->calculateDiff(
                $current['total_duration'],
                $previous['total_duration'],
                'time'
            ),
            'sessions' => $this->calculateDiff(
                $current['total_sessions'],
                $previous['total_sessions']
            ),
            'pace' => $this->calculatePaceDiff(
                $current['avg_pace'],
                $previous['avg_pace']
            ),
        ];
    }

    /**
     * Calcular diferencia entre dos valores
     */
    protected function calculateDiff($current, $previous, $type = 'number'): array
    {
        $diff = $current - $previous;
        $percentage = $previous > 0 ? (($diff / $previous) * 100) : 0;

        // Determinar tendencia
        $trend = 'stable';
        if (abs($percentage) >= 5) { // Cambio significativo: > 5%
            $trend = $diff > 0 ? 'up' : 'down';
        }

        return [
            'current' => $current,
            'previous' => $previous,
            'diff' => $type === 'time' ? $diff : round($diff, 2),
            'percentage' => round($percentage, 1),
            'trend' => $trend,
            'formatted_diff' => $type === 'time'
                ? $this->metricsService->formatDuration(abs($diff))
                : round($diff, 2),
        ];
    }

    /**
     * Calcular diferencia de pace (lógica invertida: menor es mejor)
     */
    protected function calculatePaceDiff($currentPace, $previousPace): array
    {
        if (!$currentPace || !$previousPace) {
            return [
                'current' => $currentPace,
                'previous' => $previousPace,
                'diff' => 0,
                'percentage' => 0,
                'trend' => 'stable',
                'improved' => false,
            ];
        }

        $diff = $currentPace - $previousPace; // Positivo = más lento, Negativo = más rápido
        $percentage = abs(($diff / $previousPace) * 100);

        // Para pace, una disminución es mejora
        $improved = $diff < 0;
        $trend = 'stable';
        if ($percentage >= 5) {
            $trend = $improved ? 'up' : 'down'; // up = mejora
        }

        return [
            'current' => round($currentPace),
            'previous' => round($previousPace),
            'diff' => round($diff),
            'percentage' => round($percentage, 1),
            'trend' => $trend,
            'improved' => $improved,
            'formatted_current' => $this->metricsService->formatPace(round($currentPace)),
            'formatted_previous' => $this->metricsService->formatPace(round($previousPace)),
        ];
    }

    /**
     * Generar insights automáticos
     * Solo usa workouts completados para insights (excluye planned y skipped)
     */
    public function getInsights(Collection $workouts, User $user): array
    {
        $insights = [];

        // Filtrar solo completados para insights
        $completedWorkouts = $workouts->where('status', 'completed');

        if ($completedWorkouts->isEmpty()) {
            return $insights;
        }

        // Insight 1: Mejor entrenamiento del período (mayor distancia)
        $bestWorkout = $completedWorkouts->sortByDesc('distance')->first();
        if ($bestWorkout) {
            $insights[] = [
                'icon' => '🏆',
                'message' => "Tu mejor entrenamiento: {$bestWorkout->distance} km el " .
                            $bestWorkout->date->format('d/m'),
            ];
        }

        // Insight 2: Racha de días consecutivos en el período
        $streak = $this->calculatePeriodStreak($completedWorkouts);
        if ($streak >= 3) {
            $insights[] = [
                'icon' => '🔥',
                'message' => "Racha de {$streak} días consecutivos de entrenamiento",
            ];
        }

        // Insight 3: Pace más rápido
        $fastestWorkout = $completedWorkouts->where('avg_pace', '>', 0)->sortBy('avg_pace')->first();
        if ($fastestWorkout) {
            $insights[] = [
                'icon' => '⚡',
                'message' => "Tu mejor pace: " . $fastestWorkout->formattedPace .
                            " /km el " . $fastestWorkout->date->format('d/m'),
            ];
        }

        // Insight 4: Tipo de entrenamiento más frecuente
        $typeDistribution = $completedWorkouts->groupBy('type');
        if ($typeDistribution->count() > 1) {
            $mostFrequent = $typeDistribution->sortByDesc(fn($group) => $group->count())->first();
            if ($mostFrequent && $mostFrequent->count() >= 2) {
                $typeLabel = $mostFrequent->first()->typeLabel;
                $insights[] = [
                    'icon' => '📊',
                    'message' => "Tipo más frecuente: {$typeLabel} ({$mostFrequent->count()} sesiones)",
                ];
            }
        }

        // Insight 5: Entrenamiento más largo
        $longestDuration = $completedWorkouts->sortByDesc('duration')->first();
        if ($longestDuration && $longestDuration->duration >= 3600) { // >= 1 hora
            $insights[] = [
                'icon' => '⏱️',
                'message' => "Tu sesión más larga: " . $longestDuration->formattedDuration,
            ];
        }

        return $insights;
    }

    /**
     * Calcular racha de días consecutivos dentro de un período
     */
    protected function calculatePeriodStreak(Collection $workouts): int
    {
        $dates = $workouts->pluck('date')
            ->map(fn($date) => $date->format('Y-m-d'))
            ->unique()
            ->sort()
            ->values();

        if ($dates->isEmpty()) {
            return 0;
        }

        $maxStreak = 1;
        $currentStreak = 1;

        for ($i = 1; $i < $dates->count(); $i++) {
            $prevDate = Carbon::parse($dates[$i - 1]);
            $currentDate = Carbon::parse($dates[$i]);

            if ($currentDate->diffInDays($prevDate) === 1) {
                $currentStreak++;
                $maxStreak = max($maxStreak, $currentStreak);
            } else {
                $currentStreak = 1;
            }
        }

        return $maxStreak;
    }
}

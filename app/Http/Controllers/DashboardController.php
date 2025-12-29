<?php

namespace App\Http\Controllers;

use App\Services\MetricsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    protected $metricsService;

    public function __construct(MetricsService $metricsService)
    {
        $this->metricsService = $metricsService;
    }

    public function index()
    {
        $user = Auth::user();

        $cacheKey = "dashboard_data_user_{$user->id}_week_" . now()->weekOfYear();
        $cacheTTL = now()->addMinutes(5);

        $dashboardData = Cache::remember($cacheKey, $cacheTTL, function () use ($user) {
            // Métricas de la semana usando el service
            $weekStats = $this->metricsService->getWeeklyMetrics($user);

            // Últimos 5 entrenamientos (with eager loading)
            $recentWorkouts = $this->metricsService->getRecentWorkouts($user, 5);

            // Próxima carrera
            $nextRace = $user->races()->upcoming()->first();

            // Objetivos activos (with eager loading for race relationship)
            $activeGoals = $user->goals()->with('race')->active()->limit(3)->get();

            // Estadísticas de cumplimiento de la semana
            $weeklyCompletion = [
                'planned' => $user->workouts()->thisWeekPlanned()->count(),
                'completed' => $user->workouts()->thisWeekCompleted()->count(),
                'skipped' => $user->workouts()->thisWeek()->skipped()->count(),
            ];
            $weeklyCompletion['total'] = $weeklyCompletion['planned'] + $weeklyCompletion['completed'];
            $weeklyCompletion['percentage'] = $weeklyCompletion['total'] > 0
                ? round(($weeklyCompletion['completed'] / $weeklyCompletion['total']) * 100)
                : 0;

            return compact('weekStats', 'recentWorkouts', 'nextRace', 'activeGoals', 'weeklyCompletion');
        });

        return view('dashboard', $dashboardData);
    }
}

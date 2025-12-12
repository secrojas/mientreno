<?php

namespace App\Http\Controllers;

use App\Services\MetricsService;
use Illuminate\Support\Facades\Auth;

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

        // Métricas de la semana usando el service
        $weekStats = $this->metricsService->getWeeklyMetrics($user);

        // Últimos 5 entrenamientos
        $recentWorkouts = $this->metricsService->getRecentWorkouts($user, 5);

        // Próxima carrera
        $nextRace = $user->races()->upcoming()->first();

        // Objetivos activos
        $activeGoals = $user->goals()->active()->limit(3)->get();

        return view('dashboard', compact('weekStats', 'recentWorkouts', 'nextRace', 'activeGoals'));
    }
}

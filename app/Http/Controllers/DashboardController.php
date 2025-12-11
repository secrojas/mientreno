<?php

namespace App\Http\Controllers;

use App\Models\Workout;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Workouts de esta semana
        $thisWeekWorkouts = $user->workouts()->thisWeek()->get();

        // Métricas de la semana
        $weekStats = [
            'total_distance' => $thisWeekWorkouts->sum('distance'),
            'total_duration' => $thisWeekWorkouts->sum('duration'),
            'total_workouts' => $thisWeekWorkouts->count(),
            'avg_pace' => $thisWeekWorkouts->avg('avg_pace'),
        ];

        // Últimos 5 entrenamientos
        $recentWorkouts = $user->workouts()
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact('weekStats', 'recentWorkouts'));
    }
}

<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $coach = Auth::user();
        $business = $coach->business;

        // Si el coach no tiene business aún, redirigir a crear uno
        // (esto será parte del SPRINT 2)
        if (!$business) {
            return view('coach.dashboard', [
                'hasNoBusiness' => true,
                'coach' => $coach
            ]);
        }

        // Obtener alumnos del business (runners)
        $students = $business->users()
            ->where('role', 'runner')
            ->where('id', '!=', $coach->id)
            ->get();

        $totalStudents = $students->count();

        // Métricas agregadas de alumnos
        $studentMetrics = [
            'total_workouts_this_week' => $students->sum(function ($student) {
                return $student->workouts()
                    ->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])
                    ->count();
            }),
            'total_distance_this_week' => $students->sum(function ($student) {
                return $student->workouts()
                    ->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])
                    ->sum('distance');
            }),
            'active_students_this_week' => $students->filter(function ($student) {
                return $student->workouts()
                    ->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])
                    ->exists();
            })->count(),
        ];

        // Top 3 alumnos por kilómetros esta semana
        $topStudents = $students->map(function ($student) {
            $weekDistance = $student->workouts()
                ->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])
                ->sum('distance');

            return [
                'student' => $student,
                'distance' => $weekDistance
            ];
        })
        ->sortByDesc('distance')
        ->take(3)
        ->filter(fn($item) => $item['distance'] > 0);

        // Alumnos inactivos (sin entrenamientos en las últimas 2 semanas)
        $inactiveStudents = $students->filter(function ($student) {
            $lastWorkout = $student->workouts()->latest('date')->first();
            if (!$lastWorkout) {
                return true;
            }
            return $lastWorkout->date->lt(now()->subWeeks(2));
        })->take(5);

        // Actividad reciente de alumnos (últimos 10 entrenamientos)
        $recentActivity = DB::table('workouts')
            ->join('users', 'workouts.user_id', '=', 'users.id')
            ->where('users.business_id', $business->id)
            ->where('users.role', 'runner')
            ->select('workouts.*', 'users.name as student_name', 'users.id as student_id')
            ->orderBy('workouts.date', 'desc')
            ->limit(10)
            ->get();

        // Training Groups (SPRINT 3)
        $trainingGroups = $business->trainingGroups()
            ->active()
            ->withCount('members')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('coach.dashboard', compact(
            'coach',
            'business',
            'totalStudents',
            'studentMetrics',
            'topStudents',
            'inactiveStudents',
            'recentActivity',
            'trainingGroups'
        ));
    }
}

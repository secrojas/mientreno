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
        $studentIds = $business->users()
            ->where('role', 'runner')
            ->where('id', '!=', $coach->id)
            ->pluck('id');

        $totalStudents = $studentIds->count();

        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();

        // Métricas agregadas de alumnos - usando query única optimizada
        $weekMetrics = DB::table('workouts')
            ->whereIn('user_id', $studentIds)
            ->whereBetween('date', [$weekStart, $weekEnd])
            ->selectRaw('
                COUNT(*) as total_workouts,
                SUM(distance) as total_distance,
                COUNT(DISTINCT user_id) as active_students
            ')
            ->first();

        $studentMetrics = [
            'total_workouts_this_week' => $weekMetrics->total_workouts ?? 0,
            'total_distance_this_week' => $weekMetrics->total_distance ?? 0,
            'active_students_this_week' => $weekMetrics->active_students ?? 0,
        ];

        // Top 3 alumnos por kilómetros esta semana - usando query optimizada
        $topStudentsData = DB::table('workouts')
            ->join('users', 'workouts.user_id', '=', 'users.id')
            ->whereIn('workouts.user_id', $studentIds)
            ->whereBetween('workouts.date', [$weekStart, $weekEnd])
            ->selectRaw('
                users.id,
                users.name,
                SUM(workouts.distance) as total_distance
            ')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_distance')
            ->limit(3)
            ->get();

        $topStudents = $topStudentsData->map(function ($item) {
            return [
                'student' => (object)['id' => $item->id, 'name' => $item->name],
                'distance' => $item->total_distance
            ];
        });

        // Alumnos inactivos - usando query optimizada
        $twoWeeksAgo = now()->subWeeks(2);
        $inactiveStudentIds = DB::table('users')
            ->leftJoin('workouts', function($join) use ($twoWeeksAgo) {
                $join->on('users.id', '=', 'workouts.user_id')
                     ->where('workouts.date', '>=', $twoWeeksAgo);
            })
            ->whereIn('users.id', $studentIds)
            ->whereNull('workouts.id')
            ->limit(5)
            ->pluck('users.id');

        $inactiveStudents = User::whereIn('id', $inactiveStudentIds)->get();

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

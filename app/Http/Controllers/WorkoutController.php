<?php

namespace App\Http\Controllers;

use App\Models\Workout;
use App\Services\GoalProgressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkoutController extends Controller
{
    protected $goalProgressService;

    public function __construct(GoalProgressService $goalProgressService)
    {
        $this->goalProgressService = $goalProgressService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Auth::user()->workouts();

        // Filtrar por tipo
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filtrar por estado
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtrar por rango de fechas
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        // Buscar por notas
        if ($request->filled('search')) {
            $query->where('notes', 'like', '%' . $request->search . '%');
        }

        // Eager load race relationship to avoid N+1
        $workouts = $query->with('race')->orderBy('date', 'desc')->paginate(20);

        // Obtener tipos para el filtro
        $types = Workout::typeLabels();

        return view('workouts.index', compact('workouts', 'types'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $types = Workout::typeLabels();
        $upcomingRaces = Auth::user()->races()->upcoming()->get();
        return view('workouts.create', compact('types', 'upcomingRaces'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:easy_run,intervals,tempo,long_run,recovery,race,training_run',
            'status' => 'nullable|in:planned,completed',
            'distance' => 'required|numeric|min:0|max:999', // Permite 0 para workouts planificados/salteados
            'duration' => 'nullable|integer|min:0', // Permite 0 para workouts planificados/salteados
            'avg_heart_rate' => 'nullable|integer|min:40|max:250',
            'elevation_gain' => 'nullable|integer|min:0',
            'difficulty' => 'nullable|integer|min:1|max:5',
            'notes' => 'nullable|string|max:5000',
            'race_id' => 'nullable|exists:races,id',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status'] = $validated['status'] ?? 'completed';

        // Calcular pace automáticamente solo si hay duration y distance > 0
        if (isset($validated['duration']) && $validated['duration'] > 0 && $validated['distance'] > 0) {
            $validated['avg_pace'] = Workout::calculatePace($validated['distance'], $validated['duration']);
        } else {
            $validated['avg_pace'] = null;
        }

        // Si no tiene difficulty, poner 3 por defecto
        if (!isset($validated['difficulty'])) {
            $validated['difficulty'] = 3;
        }

        Workout::create($validated);

        // Recalcular progreso de goals del usuario
        $this->goalProgressService->updateUserGoalsProgress(Auth::user());

        $message = $validated['status'] === 'planned'
            ? 'Entrenamiento planificado creado!'
            : 'Entrenamiento creado exitosamente!';

        return redirect()->route('workouts.index')->with('success', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(Workout $workout)
    {
        // Verificar que el workout pertenece al usuario autenticado
        if ($workout->user_id !== Auth::id()) {
            abort(403);
        }

        return view('workouts.show', compact('workout'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Workout $workout)
    {
        // Verificar que el workout pertenece al usuario autenticado
        if ($workout->user_id !== Auth::id()) {
            abort(403);
        }

        $types = Workout::typeLabels();
        $upcomingRaces = Auth::user()->races()->upcoming()->get();
        return view('workouts.edit', compact('workout', 'types', 'upcomingRaces'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Workout $workout)
    {
        // Verificar que el workout pertenece al usuario autenticado
        if ($workout->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:easy_run,intervals,tempo,long_run,recovery,race,training_run',
            'distance' => 'required|numeric|min:0|max:999', // Permite 0 para workouts planificados/salteados
            'duration' => 'required|integer|min:0', // Permite 0 para workouts planificados/salteados
            'avg_heart_rate' => 'nullable|integer|min:40|max:250',
            'elevation_gain' => 'nullable|integer|min:0',
            'difficulty' => 'required|integer|min:1|max:5',
            'notes' => 'nullable|string|max:5000',
            'race_id' => 'nullable|exists:races,id',
        ]);

        // Recalcular pace solo si distance y duration son > 0
        if ($validated['distance'] > 0 && $validated['duration'] > 0) {
            $validated['avg_pace'] = Workout::calculatePace($validated['distance'], $validated['duration']);
        } else {
            $validated['avg_pace'] = null;
        }

        $workout->update($validated);

        // Recalcular progreso de goals del usuario
        $this->goalProgressService->updateUserGoalsProgress(Auth::user());

        return redirect()->route('workouts.index')->with('success', 'Entrenamiento actualizado exitosamente!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Workout $workout)
    {
        // Verificar que el workout pertenece al usuario autenticado
        if ($workout->user_id !== Auth::id()) {
            abort(403);
        }

        $workout->delete();

        // Recalcular progreso de goals del usuario
        $this->goalProgressService->updateUserGoalsProgress(Auth::user());

        return redirect()->route('workouts.index')->with('success', 'Entrenamiento eliminado exitosamente!');
    }

    /**
     * Show form to mark workout as completed
     */
    public function showMarkCompleted(Workout $workout)
    {
        // Verificar ownership
        if ($workout->user_id !== Auth::id()) {
            abort(403);
        }

        // Solo workouts planificados pueden marcarse como completados
        if (!$workout->isPlanned()) {
            return redirect()->route('workouts.index')->with('error', 'Solo entrenamientos planificados pueden marcarse como completados.');
        }

        $types = Workout::typeLabels();
        $upcomingRaces = Auth::user()->races()->upcoming()->get();

        return view('workouts.mark-completed', compact('workout', 'types', 'upcomingRaces'));
    }

    /**
     * Mark workout as completed
     */
    public function markCompleted(Request $request, Workout $workout)
    {
        // Verificar ownership
        if ($workout->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'distance' => 'required|numeric|min:0|max:999', // Permite 0
            'duration' => 'required|integer|min:0', // Permite 0
            'avg_heart_rate' => 'nullable|integer|min:40|max:250',
            'elevation_gain' => 'nullable|integer|min:0',
            'difficulty' => 'required|integer|min:1|max:5',
            'notes' => 'nullable|string|max:5000',
        ]);

        $workout->markAsCompleted($validated);

        // Recalcular progreso de goals del usuario
        $this->goalProgressService->updateUserGoalsProgress(Auth::user());

        return redirect()->route('workouts.index')->with('success', 'Entrenamiento marcado como completado!');
    }

    /**
     * Mark workout as skipped
     */
    public function markSkipped(Request $request, Workout $workout)
    {
        // Verificar ownership
        if ($workout->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'skip_reason' => 'nullable|string|max:255',
        ]);

        $workout->markAsSkipped($validated['skip_reason'] ?? null);

        // Recalcular progreso de goals del usuario
        $this->goalProgressService->updateUserGoalsProgress(Auth::user());

        return redirect()->route('workouts.index')->with('success', 'Entrenamiento marcado como saltado.');
    }
}

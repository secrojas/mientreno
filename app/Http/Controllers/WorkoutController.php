<?php

namespace App\Http\Controllers;

use App\Models\Workout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkoutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $workouts = Auth::user()
            ->workouts()
            ->orderBy('date', 'desc')
            ->paginate(20);

        return view('workouts.index', compact('workouts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $types = Workout::typeLabels();
        return view('workouts.create', compact('types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:easy_run,intervals,tempo,long_run,recovery,race',
            'distance' => 'required|numeric|min:0.1|max:999',
            'duration' => 'required|integer|min:1',
            'avg_heart_rate' => 'nullable|integer|min:40|max:250',
            'elevation_gain' => 'nullable|integer|min:0',
            'difficulty' => 'required|integer|min:1|max:5',
            'notes' => 'nullable|string|max:5000',
        ]);

        // Calcular pace automáticamente
        $validated['avg_pace'] = Workout::calculatePace($validated['distance'], $validated['duration']);
        $validated['user_id'] = Auth::id();

        Workout::create($validated);

        return redirect()->route('workouts.index')->with('success', 'Entrenamiento creado exitosamente!');
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
        return view('workouts.edit', compact('workout', 'types'));
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
            'type' => 'required|in:easy_run,intervals,tempo,long_run,recovery,race',
            'distance' => 'required|numeric|min:0.1|max:999',
            'duration' => 'required|integer|min:1',
            'avg_heart_rate' => 'nullable|integer|min:40|max:250',
            'elevation_gain' => 'nullable|integer|min:0',
            'difficulty' => 'required|integer|min:1|max:5',
            'notes' => 'nullable|string|max:5000',
        ]);

        // Recalcular pace
        $validated['avg_pace'] = Workout::calculatePace($validated['distance'], $validated['duration']);

        $workout->update($validated);

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

        return redirect()->route('workouts.index')->with('success', 'Entrenamiento eliminado exitosamente!');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Race;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RaceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Auth::user()->races();

        // Filtrar por status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtrar por distancia
        if ($request->filled('distance')) {
            $query->where('distance', $request->distance);
        }

        // Separar en upcoming y past
        $upcomingRaces = Auth::user()->races()->upcoming()->get();
        $pastRaces = Auth::user()->races()->past()->paginate(15);

        $statusOptions = Race::statusOptions();
        $commonDistances = Race::commonDistances();

        return view('races.index', compact('upcomingRaces', 'pastRaces', 'statusOptions', 'commonDistances'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $statusOptions = Race::statusOptions();
        $commonDistances = Race::commonDistances();

        return view('races.create', compact('statusOptions', 'commonDistances'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'distance' => 'required|numeric|min:0.1|max:999',
            'date' => 'required|date',
            'location' => 'nullable|string|max:255',
            'target_time' => 'nullable|integer|min:1',
            'status' => 'required|in:upcoming,completed,dns,dnf',
            'notes' => 'nullable|string|max:5000',
        ]);

        $validated['user_id'] = Auth::id();

        Race::create($validated);

        return redirect()->route('races.index')->with('success', 'Carrera creada exitosamente!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Race $race)
    {
        // Verificar ownership
        if ($race->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        return view('races.show', compact('race'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Race $race)
    {
        // Verificar ownership
        if ($race->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        $statusOptions = Race::statusOptions();
        $commonDistances = Race::commonDistances();

        return view('races.edit', compact('race', 'statusOptions', 'commonDistances'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Race $race)
    {
        // Verificar ownership
        if ($race->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'distance' => 'required|numeric|min:0.1|max:999',
            'date' => 'required|date',
            'location' => 'nullable|string|max:255',
            'target_time' => 'nullable|integer|min:1',
            'actual_time' => 'nullable|integer|min:1',
            'position' => 'nullable|integer|min:1',
            'status' => 'required|in:upcoming,completed,dns,dnf',
            'notes' => 'nullable|string|max:5000',
        ]);

        $race->update($validated);

        return redirect()->route('races.index')->with('success', 'Carrera actualizada exitosamente!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Race $race)
    {
        // Verificar ownership
        if ($race->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        $race->delete();

        return redirect()->route('races.index')->with('success', 'Carrera eliminada exitosamente!');
    }
}

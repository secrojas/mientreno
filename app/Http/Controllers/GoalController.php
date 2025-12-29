<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\Race;
use App\Services\GoalProgressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoalController extends Controller
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
        $query = Auth::user()->goals();

        // Filtrar por status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtrar por tipo
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Eager load race relationship to avoid N+1
        $goals = $query->with('race')
            ->orderBy('status', 'asc')
            ->orderBy('target_date', 'asc')
            ->paginate(15);

        $statusOptions = Goal::statusOptions();
        $typeOptions = Goal::typeOptions();

        return view('goals.index', compact('goals', 'statusOptions', 'typeOptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $statusOptions = Goal::statusOptions();
        $typeOptions = Goal::typeOptions();
        $upcomingRaces = Auth::user()->races()->upcoming()->get();

        return view('goals.create', compact('statusOptions', 'typeOptions', 'upcomingRaces'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:race,distance,pace,frequency',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'target_value' => 'required|json',
            'target_date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'race_id' => 'nullable|exists:races,id',
            'status' => 'required|in:active,completed,abandoned,paused',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['target_value'] = json_decode($validated['target_value'], true);

        $goal = Goal::create($validated);

        // Calcular progreso automáticamente
        $this->goalProgressService->updateGoalProgress($goal);

        return redirect()->route('goals.index')->with('success', 'Objetivo creado exitosamente!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Goal $goal)
    {
        // Verificar ownership
        if ($goal->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        return view('goals.show', compact('goal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Goal $goal)
    {
        // Verificar ownership
        if ($goal->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        $statusOptions = Goal::statusOptions();
        $typeOptions = Goal::typeOptions();
        $upcomingRaces = Auth::user()->races()->upcoming()->get();

        return view('goals.edit', compact('goal', 'statusOptions', 'typeOptions', 'upcomingRaces'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Goal $goal)
    {
        // Verificar ownership
        if ($goal->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        $validated = $request->validate([
            'type' => 'required|in:race,distance,pace,frequency',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'target_value' => 'required|json',
            'target_date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'race_id' => 'nullable|exists:races,id',
            'status' => 'required|in:active,completed,abandoned,paused',
        ]);

        // Decodificar JSON field
        $validated['target_value'] = json_decode($validated['target_value'], true);

        $goal->update($validated);

        // Recalcular progreso automáticamente
        $this->goalProgressService->updateGoalProgress($goal);

        return redirect()->route('goals.index')->with('success', 'Objetivo actualizado exitosamente!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Goal $goal)
    {
        // Verificar ownership
        if ($goal->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        $goal->delete();

        return redirect()->route('goals.index')->with('success', 'Objetivo eliminado exitosamente!');
    }
}

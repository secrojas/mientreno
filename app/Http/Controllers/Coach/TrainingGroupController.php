<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\TrainingGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TrainingGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        // Verificar que el usuario sea coach y tenga business
        if (!$user->business_id) {
            return redirect()->route('coach.business.create')
                ->with('error', 'Debes crear un negocio primero antes de gestionar grupos.');
        }

        // Obtener grupos del business del coach
        $groups = TrainingGroup::where('business_id', $user->business_id)
            ->with(['coach', 'members'])
            ->withCount('members')
            ->orderBy('is_active', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('coach.groups.index', compact('groups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();

        if (!$user->business_id) {
            return redirect()->route('coach.business.create')
                ->with('error', 'Debes crear un negocio primero.');
        }

        return view('coach.groups.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'level' => 'required|in:beginner,intermediate,advanced',
            'max_members' => 'nullable|integer|min:1|max:200',
            'is_active' => 'boolean',
        ]);

        $user = auth()->user();

        if (!$user->business_id) {
            return back()->with('error', 'Debes tener un negocio para crear grupos.');
        }

        // Crear el grupo
        $group = TrainingGroup::create([
            'business_id' => $user->business_id,
            'coach_id' => $user->id,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'level' => $validated['level'],
            'max_members' => $validated['max_members'] ?? null,
            'schedule' => null, // Por ahora sin horarios
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('coach.groups.show', $group)
            ->with('success', 'Grupo de entrenamiento creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TrainingGroup $group)
    {
        // Verificar ownership
        if ($group->business_id !== auth()->user()->business_id) {
            abort(403, 'No tienes permiso para ver este grupo.');
        }

        // Cargar relaciones
        $group->load(['coach', 'members', 'business']);

        // Obtener alumnos disponibles para agregar (del mismo business, que no estén ya en el grupo)
        $availableStudents = User::where('business_id', $group->business_id)
            ->where('role', 'runner')
            ->whereNotIn('id', $group->members->pluck('id'))
            ->orderBy('name')
            ->get();

        // Obtener estadísticas de entrenamientos del grupo
        $groupWorkouts = \App\Models\Workout::where('training_group_id', $group->id)
            ->count();

        $totalDistance = \App\Models\Workout::where('training_group_id', $group->id)
            ->sum('distance');

        return view('coach.groups.show', compact('group', 'availableStudents', 'groupWorkouts', 'totalDistance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TrainingGroup $group)
    {
        // Verificar ownership
        if ($group->business_id !== auth()->user()->business_id) {
            abort(403, 'No tienes permiso para editar este grupo.');
        }

        return view('coach.groups.edit', compact('group'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TrainingGroup $group)
    {
        // Verificar ownership
        if ($group->business_id !== auth()->user()->business_id) {
            abort(403, 'No tienes permiso para actualizar este grupo.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'level' => 'required|in:beginner,intermediate,advanced',
            'max_members' => 'nullable|integer|min:1|max:200',
            'is_active' => 'boolean',
        ]);

        $group->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'level' => $validated['level'],
            'max_members' => $validated['max_members'] ?? null,
            'is_active' => $validated['is_active'] ?? $group->is_active,
        ]);

        return redirect()->route('coach.groups.show', $group)
            ->with('success', 'Grupo actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TrainingGroup $group)
    {
        // Verificar ownership
        if ($group->business_id !== auth()->user()->business_id) {
            abort(403, 'No tienes permiso para eliminar este grupo.');
        }

        // Soft delete: marcar como inactivo en lugar de eliminar
        $group->update(['is_active' => false]);

        return redirect()->route('coach.groups.index')
            ->with('success', 'Grupo desactivado exitosamente.');
    }

    /**
     * Agregar un miembro al grupo
     */
    public function addMember(Request $request, TrainingGroup $group)
    {
        // Verificar ownership
        if ($group->business_id !== auth()->user()->business_id) {
            abort(403, 'No tienes permiso para gestionar este grupo.');
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($validated['user_id']);

        // Verificar que el usuario sea del mismo business
        if ($user->business_id !== $group->business_id) {
            return back()->with('error', 'El alumno no pertenece al mismo negocio.');
        }

        // Verificar que sea runner
        if ($user->role !== 'runner') {
            return back()->with('error', 'Solo se pueden agregar alumnos (role: runner) a grupos.');
        }

        // Verificar límite de miembros
        if ($group->isFull()) {
            return back()->with('error', "El grupo ha alcanzado su límite de {$group->max_members} miembros.");
        }

        // Verificar si ya está en el grupo
        if ($group->members->contains($user->id)) {
            return back()->with('error', 'El alumno ya está en este grupo.');
        }

        // Agregar al grupo
        $group->members()->attach($user->id, [
            'joined_at' => now(),
            'is_active' => true,
        ]);

        return back()->with('success', "Alumno {$user->name} agregado al grupo exitosamente.");
    }

    /**
     * Remover un miembro del grupo
     */
    public function removeMember(TrainingGroup $group, User $user)
    {
        // Verificar ownership
        if ($group->business_id !== auth()->user()->business_id) {
            abort(403, 'No tienes permiso para gestionar este grupo.');
        }

        // Verificar que el usuario esté en el grupo
        if (!$group->members->contains($user->id)) {
            return back()->with('error', 'El alumno no está en este grupo.');
        }

        // Remover del grupo (soft: marcar como inactivo)
        $group->members()->updateExistingPivot($user->id, [
            'is_active' => false,
        ]);

        return back()->with('success', "Alumno {$user->name} removido del grupo.");
    }
}

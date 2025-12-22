<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BusinessController extends Controller
{
    /**
     * Mostrar el business del coach (si existe)
     */
    public function index()
    {
        $coach = Auth::user();
        $business = $coach->business;

        if (!$business) {
            return redirect()->route('coach.business.create')
                ->with('info', 'Necesitás crear tu negocio primero.');
        }

        return redirect()->route('business.coach.business.show', ['business' => $business->slug]);
    }

    /**
     * Formulario para crear business
     */
    public function create()
    {
        $coach = Auth::user();

        // Si ya tiene business, redirigir al show
        if ($coach->business) {
            return redirect()->route('business.coach.business.show', ['business' => $coach->business->slug])
                ->with('info', 'Ya tenés un negocio creado.');
        }

        return view('coach.business.create');
    }

    /**
     * Guardar nuevo business
     */
    public function store(Request $request)
    {
        $coach = Auth::user();

        // Validar que no tenga business ya
        if ($coach->business) {
            return redirect()->route('business.coach.business.show', ['business' => $coach->business->slug])
                ->with('error', 'Ya tenés un negocio creado.');
        }

        // Validar datos
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'level' => 'required|in:beginner,intermediate,advanced',
            'schedule' => 'nullable|array',
            'schedule.*.day' => 'required|string',
            'schedule.*.time' => 'required|string',
            'schedule.*.duration' => 'nullable|integer|min:30|max:180',
        ]);

        // Crear business
        $business = Business::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'level' => $validated['level'],
            'schedule' => $validated['schedule'] ?? [],
            'owner_id' => $coach->id,
            'is_active' => true,
        ]);

        // Asignar business al coach
        $coach->business_id = $business->id;
        $coach->save();

        return redirect()->route('business.coach.business.show', ['business' => $business->slug])
            ->with('success', '¡Negocio creado exitosamente!');
    }

    /**
     * Mostrar detalle del business
     */
    public function show()
    {
        $coach = Auth::user();
        $business = $coach->business;

        // Verificar que tenga business
        if (!$business) {
            return redirect()->route('coach.business.create')
                ->with('info', 'Necesitás crear tu negocio primero.');
        }

        // Cargar relaciones
        $business->load(['runners']);

        return view('coach.business.show', compact('business'));
    }

    /**
     * Formulario para editar business
     */
    public function edit()
    {
        $coach = Auth::user();
        $business = $coach->business;

        // Verificar que tenga business
        if (!$business) {
            return redirect()->route('coach.business.create')
                ->with('info', 'Necesitás crear tu negocio primero.');
        }

        return view('coach.business.edit', compact('business'));
    }

    /**
     * Actualizar business
     */
    public function update(Request $request)
    {
        $coach = Auth::user();
        $business = $coach->business;

        // Verificar que tenga business
        if (!$business) {
            return redirect()->route('coach.business.create')
                ->with('error', 'No tenés un negocio para actualizar.');
        }

        // Validar datos
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'level' => 'required|in:beginner,intermediate,advanced',
            'schedule' => 'nullable|array',
            'schedule.*.day' => 'required|string',
            'schedule.*.time' => 'required|string',
            'schedule.*.duration' => 'nullable|integer|min:30|max:180',
            'is_active' => 'nullable|boolean',
        ]);

        // Actualizar
        $business->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'level' => $validated['level'],
            'schedule' => $validated['schedule'] ?? [],
            'is_active' => $validated['is_active'] ?? $business->is_active,
        ]);

        return redirect()->route('business.coach.business.show', ['business' => $business->slug])
            ->with('success', 'Negocio actualizado exitosamente.');
    }

    /**
     * Eliminar business (soft delete - solo desactivar)
     */
    public function destroy()
    {
        $coach = Auth::user();
        $business = $coach->business;

        // Verificar que tenga business
        if (!$business) {
            return redirect()->route('coach.business.create')
                ->with('error', 'No tenés un negocio para desactivar.');
        }

        // Desactivar en lugar de eliminar
        $business->update(['is_active' => false]);

        return redirect()->route('coach.dashboard')
            ->with('success', 'Negocio desactivado exitosamente.');
    }
}

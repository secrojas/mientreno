<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    /**
     * Mostrar suscripción actual del business
     */
    public function index()
    {
        $coach = Auth::user();
        $business = $coach->business;

        if (!$business) {
            return redirect()->route('coach.business.create')
                ->with('info', 'Necesitás crear tu negocio primero.');
        }

        // Obtener suscripción activa
        $subscription = $business->getActiveSubscription();

        // Cargar plan si existe
        if ($subscription) {
            $subscription->load('plan');
            $currentPlan = $subscription->plan;
        } else {
            // Sin suscripción = plan free por defecto
            $currentPlan = null;
        }

        // Estadísticas de uso
        $studentsCount = $business->runners()->count();
        $groupsCount = $business->groups()->count();

        // Límites
        $studentLimit = $subscription ? $subscription->plan->getStudentLimit() : 5;
        $groupLimit = $subscription ? $subscription->plan->getGroupLimit() : 2;

        return view('coach.subscriptions.index', compact(
            'business',
            'subscription',
            'currentPlan',
            'studentsCount',
            'groupsCount',
            'studentLimit',
            'groupLimit'
        ));
    }

    /**
     * Mostrar planes disponibles
     */
    public function plans()
    {
        $coach = Auth::user();
        $business = $coach->business;

        if (!$business) {
            return redirect()->route('coach.business.create')
                ->with('info', 'Necesitás crear tu negocio primero.');
        }

        // Obtener todos los planes activos
        $plans = SubscriptionPlan::active()->orderBy('monthly_price')->get();

        // Suscripción actual
        $currentSubscription = $business->getActiveSubscription();
        $currentPlanId = $currentSubscription ? $currentSubscription->plan_id : null;

        return view('coach.subscriptions.plans', compact('plans', 'business', 'currentPlanId'));
    }

    /**
     * Cambiar a un plan (subscribe/upgrade/downgrade)
     */
    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
        ]);

        $coach = Auth::user();
        $business = $coach->business;

        if (!$business) {
            return back()->with('error', 'No tenés un negocio asociado.');
        }

        $newPlan = SubscriptionPlan::findOrFail($validated['plan_id']);

        // Verificar que el nuevo plan no sea el mismo que el actual
        $currentSubscription = $business->getActiveSubscription();
        if ($currentSubscription && $currentSubscription->plan_id == $newPlan->id) {
            return back()->with('info', 'Ya estás suscrito a este plan.');
        }

        // Cancelar suscripción anterior si existe
        if ($currentSubscription) {
            $currentSubscription->cancel('Cambio a plan: ' . $newPlan->name);
        }

        // Crear nueva suscripción
        $startDate = now();
        $endDate = now()->addMonth(); // Por defecto, 1 mes

        Subscription::create([
            'business_id' => $business->id,
            'plan_id' => $newPlan->id,
            'status' => 'active',
            'current_period_start' => $startDate,
            'current_period_end' => $endDate,
            'next_billing_date' => $endDate->copy()->addDay(),
            'auto_renew' => true,
        ]);

        return redirect()->route('business.coach.subscriptions.index', ['business' => $business->slug])
            ->with('success', "¡Suscripción activada! Ahora estás en el plan {$newPlan->name}.");
    }

    /**
     * Cancelar suscripción actual
     */
    public function cancel(Request $request)
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $coach = Auth::user();
        $business = $coach->business;

        if (!$business) {
            return back()->with('error', 'No tenés un negocio asociado.');
        }

        $subscription = $business->getActiveSubscription();

        if (!$subscription) {
            return back()->with('info', 'No tenés una suscripción activa para cancelar.');
        }

        // Cancelar suscripción
        $subscription->cancel($validated['reason'] ?? 'Cancelación solicitada por el usuario');

        return redirect()->route('business.coach.subscriptions.index', ['business' => $business->slug])
            ->with('success', 'Suscripción cancelada. Mantiene acceso hasta el final del período actual.');
    }
}

<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'business_id',
        'plan_id',
        'status',
        'current_period_start',
        'current_period_end',
        'next_billing_date',
        'auto_renew',
        'cancellation_reason',
    ];

    protected $casts = [
        'current_period_start' => 'date',
        'current_period_end' => 'date',
        'next_billing_date' => 'date',
        'auto_renew' => 'boolean',
    ];

    /**
     * Relación: Suscripción pertenece a un negocio
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Relación: Suscripción pertenece a un plan
     */
    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    /**
     * Scope: Solo suscripciones activas
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Solo suscripciones canceladas
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope: Solo suscripciones expiradas
     */
    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    /**
     * Scope: Solo suscripciones en trial
     */
    public function scopeTrial($query)
    {
        return $query->where('status', 'trial');
    }

    /**
     * Verificar si la suscripción está activa
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Verificar si la suscripción está cancelada
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Verificar si la suscripción está expirada
     */
    public function isExpired(): bool
    {
        return $this->status === 'expired';
    }

    /**
     * Verificar si la suscripción está en trial
     */
    public function isTrial(): bool
    {
        return $this->status === 'trial';
    }

    /**
     * Verificar si la suscripción está vigente (activa o trial)
     */
    public function isValid(): bool
    {
        return in_array($this->status, ['active', 'trial']) &&
               $this->current_period_end >= now();
    }

    /**
     * Activar suscripción
     */
    public function activate(): bool
    {
        $this->status = 'active';
        return $this->save();
    }

    /**
     * Cancelar suscripción
     */
    public function cancel(string $reason = null): bool
    {
        $this->status = 'cancelled';
        $this->cancellation_reason = $reason;
        $this->auto_renew = false;
        return $this->save();
    }

    /**
     * Marcar como expirada
     */
    public function expire(): bool
    {
        $this->status = 'expired';
        $this->auto_renew = false;
        return $this->save();
    }

    /**
     * Renovar suscripción (extender período)
     */
    public function renew(int $months = 1): bool
    {
        $this->current_period_start = $this->current_period_end->copy()->addDay();
        $this->current_period_end = $this->current_period_start->copy()->addMonths($months)->subDay();
        $this->next_billing_date = $this->current_period_end->copy()->addDay();

        if ($this->isCancelled() || $this->isExpired()) {
            $this->status = 'active';
        }

        return $this->save();
    }

    /**
     * Verificar si puede agregar más estudiantes
     */
    public function canAddStudents(int $count = 1): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        $limit = $this->plan->getStudentLimit();

        // Si no hay límite (null), puede agregar ilimitados
        if ($limit === null) {
            return true;
        }

        $currentCount = $this->business->runners()->count();
        return ($currentCount + $count) <= $limit;
    }

    /**
     * Verificar si puede agregar más grupos
     */
    public function canAddGroups(int $count = 1): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        $limit = $this->plan->getGroupLimit();

        // Si no hay límite (null), puede agregar ilimitados
        if ($limit === null) {
            return true;
        }

        $currentCount = $this->business->groups()->count();
        return ($currentCount + $count) <= $limit;
    }

    /**
     * Verificar límite de almacenamiento
     */
    public function hasStorageAvailable(float $requiredGb = 0): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        $limit = $this->plan->getStorageLimitGb();

        // Si no hay límite (null), almacenamiento ilimitado
        if ($limit === null) {
            return true;
        }

        // TODO: Implementar cálculo real de almacenamiento usado
        // Por ahora asumimos que siempre hay espacio disponible
        return true;
    }

    /**
     * Obtener días restantes del período actual
     */
    public function daysRemaining(): int
    {
        return max(0, now()->diffInDays($this->current_period_end, false));
    }

    /**
     * Verificar si está próxima a vencer (7 días o menos)
     */
    public function isNearExpiration(): bool
    {
        return $this->isValid() && $this->daysRemaining() <= 7;
    }
}

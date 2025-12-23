<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Business extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'level',
        'schedule',
        'is_active',
        'owner_id',
        'settings'
    ];

    protected $casts = [
        'settings' => 'array',
        'schedule' => 'array',
        'is_active' => 'boolean'
    ];

    /**
     * Relación: usuarios del business
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relación: dueño del business (coach)
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Relación: runners (alumnos) del business
     */
    public function runners()
    {
        return $this->hasMany(User::class)->where('role', 'runner');
    }

    /**
     * Relación: grupos de entrenamiento del business
     */
    public function trainingGroups()
    {
        return $this->hasMany(TrainingGroup::class);
    }

    /**
     * Relación: grupos de entrenamiento del business (alias)
     */
    public function groups()
    {
        return $this->trainingGroups();
    }

    /**
     * Relación: suscripciones del business
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Obtener suscripción activa actual
     */
    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)
            ->whereIn('status', ['active', 'trial'])
            ->where('current_period_end', '>=', now())
            ->latest('current_period_end');
    }

    /**
     * Obtener suscripción activa (helper)
     */
    public function getActiveSubscription(): ?Subscription
    {
        return $this->activeSubscription;
    }

    /**
     * Verificar si tiene suscripción activa
     */
    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription()->exists();
    }

    /**
     * Obtener plan actual
     */
    public function getCurrentPlan(): ?SubscriptionPlan
    {
        $subscription = $this->getActiveSubscription();
        return $subscription ? $subscription->plan : null;
    }

    /**
     * Verificar si puede agregar más estudiantes
     */
    public function canAddStudents(int $count = 1): bool
    {
        $subscription = $this->getActiveSubscription();

        if (!$subscription) {
            // Sin suscripción, usar plan free por defecto (límite: 5)
            return $this->runners()->count() + $count <= 5;
        }

        return $subscription->canAddStudents($count);
    }

    /**
     * Verificar si puede agregar más grupos
     */
    public function canAddGroups(int $count = 1): bool
    {
        $subscription = $this->getActiveSubscription();

        if (!$subscription) {
            // Sin suscripción, usar plan free por defecto (límite: 2)
            return $this->groups()->count() + $count <= 2;
        }

        return $subscription->canAddGroups($count);
    }

    /**
     * Verificar límite de almacenamiento
     */
    public function hasStorageAvailable(float $requiredGb = 0): bool
    {
        $subscription = $this->getActiveSubscription();

        if (!$subscription) {
            // Sin suscripción, usar plan free por defecto (límite: 1GB)
            return true; // Por ahora siempre retorna true
        }

        return $subscription->hasStorageAvailable($requiredGb);
    }

    /**
     * Usar slug como route key
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Auto-generar slug al crear
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($business) {
            if (empty($business->slug)) {
                $business->slug = Str::slug($business->name);

                // Asegurar unicidad
                $count = 1;
                $originalSlug = $business->slug;
                while (static::where('slug', $business->slug)->exists()) {
                    $business->slug = $originalSlug . '-' . $count++;
                }
            }
        });
    }

    /**
     * Obtener label del nivel
     */
    public function getLevelLabelAttribute(): string
    {
        return match($this->level) {
            'beginner' => 'Principiante',
            'intermediate' => 'Intermedio',
            'advanced' => 'Avanzado',
            default => '-',
        };
    }
}

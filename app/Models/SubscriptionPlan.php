<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'monthly_price',
        'annual_price',
        'currency',
        'features',
        'is_active',
    ];

    protected $casts = [
        'features' => 'array',
        'monthly_price' => 'decimal:2',
        'annual_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Relación: Un plan puede tener muchas suscripciones
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'plan_id');
    }

    /**
     * Scope: Solo planes activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Obtener límite de estudiantes del plan
     */
    public function getStudentLimit(): ?int
    {
        return $this->features['student_limit'] ?? null;
    }

    /**
     * Obtener límite de grupos del plan
     */
    public function getGroupLimit(): ?int
    {
        return $this->features['group_limit'] ?? null;
    }

    /**
     * Obtener límite de almacenamiento en GB
     */
    public function getStorageLimitGb(): ?int
    {
        return $this->features['storage_limit_gb'] ?? null;
    }

    /**
     * Verificar si el plan tiene límite de estudiantes
     */
    public function hasStudentLimit(): bool
    {
        return $this->getStudentLimit() !== null;
    }

    /**
     * Verificar si el plan tiene límite de grupos
     */
    public function hasGroupLimit(): bool
    {
        return $this->getGroupLimit() !== null;
    }

    /**
     * Verificar si el plan tiene límite de almacenamiento
     */
    public function hasStorageLimit(): bool
    {
        return $this->getStorageLimitGb() !== null;
    }

    /**
     * Verificar si el plan es gratuito
     */
    public function isFree(): bool
    {
        return $this->monthly_price == 0 && $this->annual_price == 0;
    }

    /**
     * Obtener precio anual con descuento (si aplica)
     */
    public function getAnnualDiscount(): float
    {
        if ($this->monthly_price == 0 || $this->annual_price == 0) {
            return 0;
        }

        $monthlyTotal = $this->monthly_price * 12;
        $savings = $monthlyTotal - $this->annual_price;

        return round(($savings / $monthlyTotal) * 100, 1);
    }
}

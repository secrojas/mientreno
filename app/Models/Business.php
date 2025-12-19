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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingGroup extends Model
{
    protected $fillable = [
        'business_id',
        'coach_id',
        'name',
        'description',
        'schedule',
        'level',
        'max_members',
        'is_active',
    ];

    protected $casts = [
        'schedule' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Relación con el business al que pertenece el grupo
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Relación con el coach dueño del grupo
     */
    public function coach()
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    /**
     * Relación muchos a muchos con usuarios (miembros del grupo)
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'training_group_user')
            ->withTimestamps()
            ->withPivot('joined_at', 'is_active')
            ->where('role', 'runner'); // Solo alumnos
    }

    /**
     * Solo miembros activos
     */
    public function activeMembers()
    {
        return $this->members()->wherePivot('is_active', true);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForBusiness($query, $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    public function scopeForCoach($query, $coachId)
    {
        return $query->where('coach_id', $coachId);
    }

    /**
     * Accessor para traducir el nivel a español
     */
    public function getLevelLabelAttribute()
    {
        return match($this->level) {
            'beginner' => 'Principiante',
            'intermediate' => 'Intermedio',
            'advanced' => 'Avanzado',
            default => 'No especificado',
        };
    }

    /**
     * Helper para obtener el conteo de miembros activos
     */
    public function getActiveMembersCountAttribute()
    {
        return $this->activeMembers()->count();
    }

    /**
     * Verificar si el grupo está lleno
     */
    public function isFull()
    {
        if (!$this->max_members) {
            return false;
        }

        return $this->active_members_count >= $this->max_members;
    }

    /**
     * Opciones de nivel para formularios
     */
    public static function levelOptions()
    {
        return [
            'beginner' => 'Principiante',
            'intermediate' => 'Intermedio',
            'advanced' => 'Avanzado',
        ];
    }
}

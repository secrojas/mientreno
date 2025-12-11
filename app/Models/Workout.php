<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Workout extends Model
{
    protected $fillable = [
        'user_id',
        'training_group_id',
        'race_id',
        'date',
        'type',
        'distance',
        'duration',
        'avg_pace',
        'avg_heart_rate',
        'elevation_gain',
        'difficulty',
        'notes',
        'weather',
        'route',
        'is_race',
    ];

    protected $casts = [
        'date' => 'date',
        'distance' => 'decimal:2',
        'duration' => 'integer',
        'avg_pace' => 'integer',
        'avg_heart_rate' => 'integer',
        'elevation_gain' => 'integer',
        'difficulty' => 'integer',
        'weather' => 'array',
        'route' => 'array',
        'is_race' => 'boolean',
    ];

    // Relaciones

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function trainingGroup(): BelongsTo
    {
        return $this->belongsTo(TrainingGroup::class);
    }

    public function race(): BelongsTo
    {
        return $this->belongsTo(Race::class);
    }

    // Scopes

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('date', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereBetween('date', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ]);
    }

    public function scopeThisYear($query)
    {
        return $query->whereBetween('date', [
            now()->startOfYear(),
            now()->endOfYear()
        ]);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Métodos auxiliares

    /**
     * Calcula el pace promedio (seg/km) a partir de distance y duration
     */
    public static function calculatePace(float $distance, int $duration): ?int
    {
        if ($distance <= 0) {
            return null;
        }

        return (int) round($duration / $distance);
    }

    /**
     * Formatea el pace en formato MM:SS/km
     */
    public function getFormattedPaceAttribute(): string
    {
        if (!$this->avg_pace) {
            return '–';
        }

        $minutes = floor($this->avg_pace / 60);
        $seconds = $this->avg_pace % 60;

        return sprintf("%d:%02d/km", $minutes, $seconds);
    }

    /**
     * Formatea la duración en formato HH:MM:SS
     */
    public function getFormattedDurationAttribute(): string
    {
        $hours = floor($this->duration / 3600);
        $minutes = floor(($this->duration % 3600) / 60);
        $seconds = $this->duration % 60;

        if ($hours > 0) {
            return sprintf("%d:%02d:%02d", $hours, $minutes, $seconds);
        }

        return sprintf("%d:%02d", $minutes, $seconds);
    }

    /**
     * Etiquetas de tipos de entrenamiento
     */
    public static function typeLabels(): array
    {
        return [
            'training_run' => 'Entrenamiento',
            'easy_run' => 'Fondo Suave',
            'long_run' => 'Fondo Largo',
            'intervals' => 'Series/Intervalos',
            'tempo' => 'Ritmo Sostenido',            
            'recovery' => 'Recuperación',
            'race' => 'Carrera/Competencia',
        ];
    }

    /**
     * Obtiene la etiqueta del tipo
     */
    public function getTypeLabelAttribute(): string
    {
        return self::typeLabels()[$this->type] ?? $this->type;
    }
}

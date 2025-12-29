<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Workout extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'training_group_id',
        'race_id',
        'date',
        'type',
        'status',
        'distance',
        'planned_distance',
        'duration',
        'avg_pace',
        'avg_heart_rate',
        'elevation_gain',
        'difficulty',
        'notes',
        'skip_reason',
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

    public function scopePlanned($query)
    {
        return $query->where('status', 'planned');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeSkipped($query)
    {
        return $query->where('status', 'skipped');
    }

    public function scopeThisWeekPlanned($query)
    {
        return $query->planned()->whereBetween('date', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeThisWeekCompleted($query)
    {
        return $query->completed()->whereBetween('date', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
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

    /**
     * Etiquetas de status
     */
    public static function statusLabels(): array
    {
        return [
            'planned' => 'Planificado',
            'completed' => 'Completado',
            'skipped' => 'Saltado',
        ];
    }

    /**
     * Obtiene la etiqueta del status
     */
    public function getStatusLabelAttribute(): string
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }

    /**
     * Obtiene la diferencia entre plan y realidad (en km)
     */
    public function getDifferenceFromPlanAttribute(): ?float
    {
        if (!$this->planned_distance || $this->status !== 'completed') {
            return null;
        }

        return round($this->distance - $this->planned_distance, 2);
    }

    /**
     * Verifica si el workout está planificado
     */
    public function isPlanned(): bool
    {
        return $this->status === 'planned';
    }

    /**
     * Verifica si el workout está completado
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Verifica si el workout está saltado
     */
    public function isSkipped(): bool
    {
        return $this->status === 'skipped';
    }

    /**
     * Marca el workout como completado
     */
    public function markAsCompleted(array $data): bool
    {
        // Si estaba planificado, guardar la distancia planificada
        if ($this->isPlanned() && !$this->planned_distance) {
            $this->planned_distance = $this->distance;
        }

        $data['status'] = 'completed';
        $data['skip_reason'] = null;

        // Calcular pace automáticamente si tiene distance y duration > 0
        if (isset($data['distance']) && isset($data['duration']) && $data['distance'] > 0 && $data['duration'] > 0) {
            $data['avg_pace'] = self::calculatePace($data['distance'], $data['duration']);
        } else {
            $data['avg_pace'] = null;
        }

        return $this->update($data);
    }

    /**
     * Marca el workout como saltado
     */
    public function markAsSkipped(?string $reason = null): bool
    {
        return $this->update([
            'status' => 'skipped',
            'skip_reason' => $reason,
        ]);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Goal extends Model
{
    protected $fillable = [
        'user_id',
        'race_id',
        'type',
        'title',
        'description',
        'target_value',
        'target_date',
        'start_date',
        'status',
        'progress',
    ];

    protected $casts = [
        'target_value' => 'array',
        'progress' => 'array',
        'target_date' => 'date',
        'start_date' => 'date',
    ];

    // Relaciones

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function race(): BelongsTo
    {
        return $this->belongsTo(Race::class);
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeDueSoon($query, int $days = 30)
    {
        return $query->where('status', 'active')
            ->whereNotNull('target_date')
            ->whereBetween('target_date', [now(), now()->addDays($days)])
            ->orderBy('target_date', 'asc');
    }

    // Accessors

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'race' => 'Carrera',
            'distance' => 'Distancia',
            'pace' => 'Pace',
            'frequency' => 'Frecuencia',
            default => 'Desconocido',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'active' => 'Activo',
            'completed' => 'Completado',
            'abandoned' => 'Abandonado',
            'paused' => 'Pausado',
            default => 'Desconocido',
        };
    }

    public function getDaysUntilAttribute(): ?int
    {
        if (!$this->target_date || $this->status !== 'active') {
            return null;
        }

        return now()->diffInDays($this->target_date, false);
    }

    public function getProgressPercentageAttribute(): int
    {
        if (!$this->progress || !isset($this->progress['percentage'])) {
            return 0;
        }

        return min(100, max(0, (int) $this->progress['percentage']));
    }

    // Helpers

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isOverdue(): bool
    {
        return $this->status === 'active'
            && $this->target_date
            && $this->target_date < now();
    }

    public function getTargetDescription(): string
    {
        if (!$this->target_value) {
            return '';
        }

        return match($this->type) {
            'race' => $this->getRaceTargetDescription(),
            'distance' => $this->getDistanceTargetDescription(),
            'pace' => $this->getPaceTargetDescription(),
            'frequency' => $this->getFrequencyTargetDescription(),
            default => '',
        };
    }

    protected function getRaceTargetDescription(): string
    {
        if (!isset($this->target_value['time'])) {
            return '';
        }

        $time = $this->target_value['time'];
        $hours = floor($time / 3600);
        $minutes = floor(($time % 3600) / 60);
        $seconds = $time % 60;

        if ($hours > 0) {
            return sprintf("Tiempo objetivo: %d:%02d:%02d", $hours, $minutes, $seconds);
        }

        return sprintf("Tiempo objetivo: %d:%02d", $minutes, $seconds);
    }

    protected function getDistanceTargetDescription(): string
    {
        if (!isset($this->target_value['distance']) || !isset($this->target_value['period'])) {
            return '';
        }

        $distance = $this->target_value['distance'];
        $period = $this->target_value['period'];
        $periodLabel = $period === 'week' ? 'semana' : 'mes';

        return sprintf("%s km por %s", number_format($distance, 1), $periodLabel);
    }

    protected function getPaceTargetDescription(): string
    {
        if (!isset($this->target_value['pace'])) {
            return '';
        }

        $pace = $this->target_value['pace'];
        $minutes = floor($pace / 60);
        $seconds = $pace % 60;

        return sprintf("Pace objetivo: %d:%02d/km", $minutes, $seconds);
    }

    protected function getFrequencyTargetDescription(): string
    {
        if (!isset($this->target_value['sessions']) || !isset($this->target_value['period'])) {
            return '';
        }

        $sessions = $this->target_value['sessions'];
        $period = $this->target_value['period'];
        $periodLabel = $period === 'week' ? 'semana' : 'mes';

        return sprintf("%d sesiones por %s", $sessions, $periodLabel);
    }

    public static function typeOptions(): array
    {
        return [
            'race' => 'Carrera',
            'distance' => 'Distancia',
            'pace' => 'Pace',
            'frequency' => 'Frecuencia',
        ];
    }

    public static function statusOptions(): array
    {
        return [
            'active' => 'Activo',
            'completed' => 'Completado',
            'abandoned' => 'Abandonado',
            'paused' => 'Pausado',
        ];
    }
}

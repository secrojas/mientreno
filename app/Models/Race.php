<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Race extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'name',
        'distance',
        'date',
        'location',
        'target_time',
        'actual_time',
        'position',
        'notes',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'distance' => 'decimal:2',
        'target_time' => 'integer',
        'actual_time' => 'integer',
        'position' => 'integer',
    ];

    // Relaciones

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workouts(): HasMany
    {
        return $this->hasMany(Workout::class);
    }

    public function goals(): HasMany
    {
        return $this->hasMany(Goal::class);
    }

    // Scopes

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'upcoming')
            ->where('date', '>=', now())
            ->orderBy('date', 'asc');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed')
            ->orderBy('date', 'desc');
    }

    public function scopePast($query)
    {
        return $query->where('date', '<', now())
            ->orderBy('date', 'desc');
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Accessors

    public function getFormattedTargetTimeAttribute(): string
    {
        return $this->formatTime($this->target_time);
    }

    public function getFormattedActualTimeAttribute(): string
    {
        return $this->formatTime($this->actual_time);
    }

    public function getDaysUntilAttribute(): ?int
    {
        if (!$this->date || $this->status !== 'upcoming') {
            return null;
        }

        return now()->diffInDays($this->date, false);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'upcoming' => 'Próxima',
            'completed' => 'Completada',
            'dns' => 'No participó',
            'dnf' => 'No terminó',
            default => 'Desconocido',
        };
    }

    public function getDistanceLabelAttribute(): string
    {
        // Distancias comunes con labels
        $commonDistances = [
            5 => '5K',
            10 => '10K',
            15 => '15K',
            21.1 => 'Media Maratón',
            42.2 => 'Maratón',
        ];

        return $commonDistances[$this->distance] ?? $this->distance . ' km';
    }

    // Helpers

    protected function formatTime(?int $seconds): string
    {
        if (!$seconds) {
            return '–';
        }

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        if ($hours > 0) {
            return sprintf("%d:%02d:%02d", $hours, $minutes, $secs);
        }

        return sprintf("%d:%02d", $minutes, $secs);
    }

    public function hasActualTime(): bool
    {
        return !is_null($this->actual_time);
    }

    public function isPast(): bool
    {
        return $this->date < now();
    }

    public function isUpcoming(): bool
    {
        return $this->status === 'upcoming' && $this->date >= now();
    }

    public static function statusOptions(): array
    {
        return [
            'upcoming' => 'Próxima',
            'completed' => 'Completada',
            'dns' => 'No participó (DNS)',
            'dnf' => 'No terminó (DNF)',
        ];
    }

    public static function commonDistances(): array
    {
        return [
            5 => '5K',
            10 => '10K',
            15 => '15K',
            21.1 => 'Media Maratón (21.1K)',
            42.2 => 'Maratón (42.2K)',
        ];
    }
}

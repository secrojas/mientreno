<?php

namespace App\Models;

use App\Enums\ShoeCondition;
use App\Enums\ShoeUsage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shoe extends Model
{
    /** @use HasFactory<\Database\Factories\ShoeFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'brand',
        'model',
        'nickname',
        'color',
        'photo_path',
        'usage',
        'purchased_at',
        'price',
        'initial_km',
        'max_km',
        'is_default',
        'retired_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'usage' => ShoeUsage::class,
            'purchased_at' => 'date',
            'price' => 'decimal:2',
            'initial_km' => 'decimal:2',
            'max_km' => 'integer',
            'is_default' => 'boolean',
            'retired_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workouts(): HasMany
    {
        return $this->hasMany(Workout::class);
    }

    /**
     * @param  Builder<Shoe>  $query
     */
    public function scopeActive(Builder $query): void
    {
        $query->whereNull('retired_at');
    }

    /**
     * @param  Builder<Shoe>  $query
     */
    public function scopeWithUsageStats(Builder $query): void
    {
        $query->withSum(['workouts as completed_km' => fn ($workouts) => $workouts->where('status', 'completed')], 'distance')
            ->withCount(['workouts as completed_sessions' => fn ($workouts) => $workouts->where('status', 'completed')])
            ->withMax(['workouts as last_used_at' => fn ($workouts) => $workouts->where('status', 'completed')], 'date');
    }

    public function name(): string
    {
        return $this->brand.' '.$this->model;
    }

    /**
     * Km totales: los que traía al cargarla más los de los entrenamientos completados.
     */
    public function totalKm(): float
    {
        $workoutKm = $this->completed_km
            ?? $this->workouts()->where('status', 'completed')->sum('distance');

        return round((float) $this->initial_km + (float) $workoutKm, 1);
    }

    public function remainingKm(): float
    {
        return round($this->max_km - $this->totalKm(), 1);
    }

    public function usedRatio(): float
    {
        return $this->max_km > 0 ? $this->totalKm() / $this->max_km : 0;
    }

    public function condition(): ShoeCondition
    {
        return ShoeCondition::fromUsage($this->usedRatio(), $this->isRetired());
    }

    public function isRetired(): bool
    {
        return $this->retired_at !== null;
    }

    public function costPerKm(): ?float
    {
        $totalKm = $this->totalKm();

        if (! $this->price || $totalKm <= 0) {
            return null;
        }

        return round((float) $this->price / $totalKm, 2);
    }
}

<?php

namespace App\Models;

use App\Enums\MedicalDocumentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MedicalDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'doctor_id',
        'type',
        'title',
        'notes',
        'file_path',
        'original_name',
        'issued_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => MedicalDocumentType::class,
            'issued_at' => 'date',
            'expires_at' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(MedicalDocumentGroup::class, 'medical_document_group_items')
            ->withTimestamps();
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isExpiringSoon(): bool
    {
        if (! $this->expires_at || $this->isExpired()) {
            return false;
        }

        return now()->diffInDays($this->expires_at, false) <= 30;
    }

    public function getDaysUntilExpiryAttribute(): ?int
    {
        if (! $this->expires_at) {
            return null;
        }

        return (int) now()->diffInDays($this->expires_at, false);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalOrder extends Model
{
    /** @use HasFactory<\Database\Factories\MedicalOrderFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'doctor_id',
        'title',
        'notes',
        'file_path',
        'original_name',
        'issued_at',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'date',
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
}

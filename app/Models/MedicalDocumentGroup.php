<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MedicalDocumentGroup extends Model
{
    /** @use HasFactory<\Database\Factories\MedicalDocumentGroupFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'doctor_id',
        'title',
        'notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(MedicalDocument::class, 'medical_document_group_items')
            ->withTimestamps();
    }
}

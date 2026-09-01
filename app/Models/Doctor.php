<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Model
{
    /** @use HasFactory<\Database\Factories\DoctorFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'specialty',
        'phone',
        'email',
        'address',
        'notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function medicalDocuments(): HasMany
    {
        return $this->hasMany(MedicalDocument::class);
    }

    public function medicalDocumentGroups(): HasMany
    {
        return $this->hasMany(MedicalDocumentGroup::class);
    }

    public function medicalOrders(): HasMany
    {
        return $this->hasMany(MedicalOrder::class);
    }
}

<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'business_id',
        'role',
        'avatar',
        'birth_date',
        'gender',
        'weight',
        'height',
        'bio',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
            'weight' => 'decimal:2',
        ];
    }

    /**
     * Get user's age
     */
    public function getAgeAttribute(): ?int
    {
        return $this->birth_date ? $this->birth_date->age : null;
    }

    /**
     * Get avatar URL
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }

        // Fallback a iniciales
        return '';
    }

    /**
     * Get gender label
     */
    public function getGenderLabelAttribute(): string
    {
        return match($this->gender) {
            'male' => 'Masculino',
            'female' => 'Femenino',
            'other' => 'Otro',
            'prefer_not_to_say' => 'Prefiero no decir',
            default => '-',
        };
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function workouts()
    {
        return $this->hasMany(Workout::class);
    }

    public function races()
    {
        return $this->hasMany(Race::class);
    }

    public function goals()
    {
        return $this->hasMany(Goal::class);
    }
}

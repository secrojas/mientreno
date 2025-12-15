<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ReportShare extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'report_type',
        'year',
        'period',
        'token',
        'expires_at',
        'view_count',
        'last_viewed_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_viewed_at' => 'datetime',
    ];

    /**
     * Relación con el usuario
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generar un nuevo token único
     */
    public static function generateToken(): string
    {
        return Str::random(32);
    }

    /**
     * Crear un nuevo share para un reporte
     */
    public static function createShare(
        int $userId,
        string $reportType,
        int $year,
        int $period,
        int $hoursValid = 24
    ): self {
        // Buscar si ya existe un share válido
        $existingShare = self::where('user_id', $userId)
            ->where('report_type', $reportType)
            ->where('year', $year)
            ->where('period', $period)
            ->where('expires_at', '>', now())
            ->first();

        if ($existingShare) {
            return $existingShare;
        }

        // Crear nuevo share
        return self::create([
            'user_id' => $userId,
            'report_type' => $reportType,
            'year' => $year,
            'period' => $period,
            'token' => self::generateToken(),
            'expires_at' => now()->addHours($hoursValid),
        ]);
    }

    /**
     * Verificar si el share es válido
     */
    public function isValid(): bool
    {
        return $this->expires_at->isFuture();
    }

    /**
     * Incrementar contador de vistas
     */
    public function incrementViews(): void
    {
        $this->increment('view_count');
        $this->update(['last_viewed_at' => now()]);
    }

    /**
     * Obtener el URL completo del share
     */
    public function getShareUrl(): string
    {
        return url('/share/' . $this->token);
    }

    /**
     * Scope para shares válidos (no expirados)
     */
    public function scopeValid($query)
    {
        return $query->where('expires_at', '>', now());
    }

    /**
     * Scope para shares expirados
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    /**
     * Buscar share por token
     */
    public static function findByToken(string $token): ?self
    {
        return self::where('token', $token)->first();
    }

    /**
     * Buscar share válido por token
     */
    public static function findValidByToken(string $token): ?self
    {
        return self::where('token', $token)
            ->where('expires_at', '>', now())
            ->first();
    }

    /**
     * Limpiar shares expirados
     */
    public static function cleanupExpired(): int
    {
        return self::expired()->delete();
    }
}

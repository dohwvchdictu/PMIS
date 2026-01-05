<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;

class User extends Authenticatable implements FilamentUser, Auditable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'hris_id',
        'name',
        'email',
        'email_verified_at',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected $auditExclude = [
        'password',
        'remember_token',
    ];

    protected $auditTimestamps = true;
    protected $auditStrict = false;

    /**
     * Resolve the user for auditing
     */
    public static function resolveAuditUser()
    {
        try {
            // Try web guard first (your main app)
            if (auth()->guard('web')->check()) {
                return auth()->guard('web')->user();
            }

            // Try Filament guard (admin panel)
            if (class_exists(\Filament\Facades\Filament::class)) {
                if (\Filament\Facades\Filament::auth()->check()) {
                    return \Filament\Facades\Filament::auth()->user();
                }
            }

            // Try sanctum guard (if used)
            if (auth()->guard('sanctum')->check()) {
                return auth()->guard('sanctum')->user();
            }

        } catch (\Throwable $e) {
            \Log::error('User resolver error: ' . $e->getMessage());
        }

        return null;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
}
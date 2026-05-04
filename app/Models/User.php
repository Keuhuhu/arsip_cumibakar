<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Role constants — hanya 2 role
    const ROLE_SUPER_ADMIN = 'super_admin';
    const ROLE_USER        = 'user';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'nip',
        'jabatan',
        'no_hp',
        'foto',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
            'last_login_at'     => 'datetime',
        ];
    }

    // Relationships
    public function dokumens(): HasMany
    {
        return $this->hasMany(Dokumen::class, 'uploaded_by');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    // Role checks
    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isUser(): bool
    {
        return $this->role === self::ROLE_USER;
    }

    // Permissions based on role
    public function can($abilities, $arguments = [])
    {
        return parent::can($abilities, $arguments);
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            self::ROLE_SUPER_ADMIN => 'Super Admin',
            self::ROLE_USER        => 'User',
            default                => 'User',
        };
    }

    public function getRoleBadgeAttribute(): string
    {
        return match ($this->role) {
            self::ROLE_SUPER_ADMIN => 'badge-danger',
            self::ROLE_USER        => 'badge-info',
            default                => 'badge-secondary',
        };
    }
}
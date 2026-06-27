<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_PEGAWAI = 'pegawai';
    public const ROLE_JAMAAH = 'jamaah';

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'password_changed_at',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password_changed_at' => 'datetime',
    ];

    public function pegawai()
    {
        return $this->hasOne(Pegawai::class, 'email', 'email');
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    public function isPegawai(): bool
    {
        return $this->hasRole(self::ROLE_PEGAWAI);
    }

    public function isJamaah(): bool
    {
        return $this->hasRole(self::ROLE_JAMAAH);
    }

    public function getFirstNameAttribute(): string
    {
        return explode(' ', trim($this->name))[0];
    }
}

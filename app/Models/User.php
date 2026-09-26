<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property string $role
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Daftar role yang dikenal aplikasi ini.
     */
    public const ROLE = [
        'pelapor' => 'pelapor',
        'admin' => 'admin',
        'super_admin' => 'super_admin',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'foto_profil',
        'posisi',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function reports()
    {
        return $this->hasMany(
            Report::class,
            'user_id'
        );
    }

    public function statuses()
    {
        return $this->hasMany(
            TabelStatus::class,
            'user_id'
        );
    }

    /**
     * Apakah user ini punya role tertentu.
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function isPelapor(): bool
    {
        return $this->hasRole(self::ROLE['pelapor']);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(self::ROLE['admin']);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(self::ROLE['super_admin']);
    }
}
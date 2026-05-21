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

    // =========================
    // FILLABLE
    // =========================
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'foto_profil',
    ];

    // =========================
    // HIDDEN
    // =========================
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // =========================
    // CASTS
    // =========================
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // =========================
    // RELASI KE REPORT
    // =========================
    public function reports()
    {
        return $this->hasMany(
            Report::class,
            'user_id'
        );
    }

    // =========================
    // RELASI KE STATUS
    // =========================
    public function statuses()
    {
        return $this->hasMany(
            TabelStatus::class,
            'user_id'
        );
    }

    // =========================
    // ROLE HELPER
    // =========================
    public function isPelapor()
    {
        return $this->role === 'pelapor';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }
}
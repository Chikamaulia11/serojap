<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property string $role
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /* RELATION */
    public function reports()
    {
        return $this->hasMany(\App\Models\Report::class);
    }

    /* FILLABLE */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'foto_profil',
    ];

<<<<<<< HEAD
    public function laporan()
    {
        return $this->hasMany(\App\Models\TabelLaporan::class, 'user_id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
=======
    /* HIDDEN */
>>>>>>> af1bf81 (setup final)
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /* CAST */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /* ROLE HELPER */
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
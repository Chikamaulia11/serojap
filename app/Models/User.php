<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property string $role
 * @property bool $is_active
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Daftar role yang dikenal aplikasi ini.
     */
    public const ROLE = [
        'pelapor' => 'pelapor',
        'admin' => 'admin',
        'super_admin' => 'super_admin',
    ];

    /**
     * `role` sengaja TIDAK ada di $fillable.
     *
     * kolom ini menentukan seluruh batas hak akses aplikasi. Kalau
     * dibiarkanfillable, satu panggilan `User::create($request->all())`
     * atau `$user->update($request->all())` di masa depan sudah cukup
     * untuk menaikkan akun pelapor jadi super admin. Role hanya boleh
     * diisi lewat `withRole()`.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'foto_profil',
        'posisi',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Nilai bawaan untuk model yang baru dibuat di memori.
     *
     * Kolom `is_active` punya `default(true)` di level database, tapi itu
     * hanya berlaku untuk baris yang TERSIMPAN. Instance model yang baru
     * di-create di PHP tidak tahu apa pun soal default itu, jadi
     * `$user->is_active` bernilai `null` -- dan `bisaLogin()` mengembalikan
     * false karena `null` itu falsy.
     *
     * Dampaknya nyata, bukan cuma di test:
     *   - `User::withRole(...)` mengembalikan instance itu langsung, dan
     *     `DemoUserSeeder` memakainya.
     *   - Right after `RegisteredUserController::store()`, permintaan
     *     berikutnya bisa saja_MEMBAWA instance yang sama (mis. lewat
     *     `Auth::login($user)` lalu langsung dicek `bisaLogin()`), dan
     *     akun baru akan ditolak sebagai "sudah dinonaktifkan".
     *
     * Dengan declare `$attributes`, `is_active` selalu ikut ter-INSERT dan
     * selalu terbaca `true` di instance baru.
     */
    protected $attributes = [
        'is_active' => true,
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    /**
     * Buat instance user dengan role tertentu, di luar jalur mass assignment.
     */
    public static function withRole(string $role, array $attributes = []): static
    {
        // Catatan: versi lama menulis `->create($attributes)->tap(fn (self $u) => ...)`
        // dan itu selalu gagal. `Model` tidak punya method `tap()`; `tap()` ada
        // di `BuildsQueries` milik Builder/Query Builder. Jadi `__call()`
        // Model meneruskannya ke query builder, dan closure-nya menerima
        // Builder -- bukan User. Akibatnya:
        //
        //   TypeError: {closure}(): Argument #1 ($user) must be of type
        //   App\Models\User, Illuminate\Database\Eloquent\Builder given
        //
        // yang membuat SETIAP registrasi dan pembuatan akun admin 500.
        $user = static::query()->create($attributes);

        $user->forceFill(['role' => $role])->save();

        return $user;
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

    /**
     * Nama route dashboard milik role ini.
     *
     * Pemetaan role -> dashboard ini sebelumnya ditulis ulang di tiga
     * middleware (Admin, Pelapor, SuperAdmin) dan dua kali di view
     * `errors/404` -- `route('dashboard')` selalu menunjuk ke dashboard
     * pelapor, jadi admin yang membuka 404 lalu menekan "Ke Dashboard"
     * akan mendarat di halaman orang lain.
     *
     * @return string|null null kalau role-nya tidak dikenali.
     */
    public function dashboardRoute(): ?string
    {
        return match ($this->role) {
            self::ROLE['pelapor'] => 'dashboard',
            self::ROLE['admin'] => 'admin.dashboard',
            self::ROLE['super_admin'] => 'superadmin.dashboard',
            default => null,
        };
    }

    /**
     * Nama route halaman login milik role ini, untuk link "ganti akun".
     */
    public function loginRoute(): ?string
    {
        return match ($this->role) {
            self::ROLE['pelapor'] => 'login',
            self::ROLE['admin'] => 'login.admin',
            self::ROLE['super_admin'] => 'login.superadmin',
            default => 'login',
        };
    }

    /**
     * Akun yang sudah dinonaktifkan tidak boleh dipakai lagi, walau
     * passwordnya masih benar.
     */
    public function bisaLogin(): bool
    {
        return $this->is_active && ! $this->trashed();
    }

    /**
     * Jumlah akun super admin aktif besides-id.
     *
     * Dipakai untuk mencegah super admin terakhir mengunci dirinya
     * sendiri keluar dari sistem.
     */
    public static function jumlahSuperAdminAktif(?int $kecualiId = null): int
    {
        return static::query()
            ->where('role', self::ROLE['super_admin'])
            ->where('is_active', true)
            ->when($kecualiId, fn ($q) => $q->whereKeyNot($kecualiId))
            ->count();
    }
}

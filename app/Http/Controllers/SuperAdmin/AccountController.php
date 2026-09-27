<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AccountController extends Controller
{
    /**
     * Role yang boleh dibuat / dikelola dari halaman ini.
     *
     * `super_admin` sengaja TIDAK ada di daftar ini supaya tidak ada
     * jalur UI untuk membuat akun super admin baru, dan supaya akun
     * super admin tidak bisa diedit atau dinonaktifkan lewat halaman
     * ini. Satu-satunya super admin yang sah dibuat lewat seeder.
     */
    private const ROLE_TERMURAH = [
        User::ROLE['admin'],
        User::ROLE['pelapor'],
    ];

    /**
     * Halaman daftar akun admin dan pelapor.
     */
    public function index(Request $request)
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'role' => ['nullable', Rule::in(self::ROLE_TERMURAH)],
            'status' => ['nullable', Rule::in(['aktif', 'nonaktif'])],
        ], [], [
            'search' => 'pencarian',
            'role' => 'role',
            'status' => 'status',
        ]);

        $perHalaman = 15;

        $users = User::query()
            ->withTrashed()
            ->whereIn('role', self::ROLE_TERMURAH)
            ->when($request->search, function ($query) use ($request) {
                $search = trim($request->search);

                $query->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('posisi', 'like', "%{$search}%");
                });
            })
            ->when($request->role, fn ($query) => $query->where('role', $request->role))
            ->when($request->status === 'nonaktif', fn ($query) => $query->where(fn ($q) => $q->where('is_active', false)->orWhereNotNull('deleted_at')))
            ->when($request->status === 'aktif', fn ($query) => $query->where('is_active', true)->whereNull('deleted_at'))
            ->orderByDesc('id')
            ->paginate($perHalaman)
            ->withQueryString();

        /*
         * Angka di kartu ringkasan harus dihitung terpisah dari
         * `$users->count()`.
         *
         * `$users` itu Paginator, jadi `count()`-nya mengembalikan
         * jumlah item DI HALAMAN INI SAJA (15), bukan jumlah seluruh
         * akun. Kalau kartu ringkasan memakainya, begitu pengguna
         * membuka halaman 2 angka "Total Admin" ikut turun -- dan
         * begitu juga saat pencarian atau filter aktif, karena
         * jumlahnya ikut terpotong. Yang tampil di sini adalah
         * seluruh data, bukan hasil filter.
         *
         * `withTrashed()` dipakai supaya akun yang dinonaktifkan
         * tetap dihitung: datanya masih ada dan masih bisa dipulihkan.
         */
        $semua = fn () => User::withTrashed()->whereIn('role', self::ROLE_TERMURAH);

        $ringkasan = [
            'admin' => $semua()->where('role', User::ROLE['admin'])->count(),
            'pelapor' => $semua()->where('role', User::ROLE['pelapor'])->count(),
            'aktif' => $semua()->where('is_active', true)->whereNull('deleted_at')->count(),
            'nonaktif' => $semua()->where(fn ($q) => $q->where('is_active', false)->orWhereNotNull('deleted_at'))->count(),
        ];

        $ringkasan['total'] = $ringkasan['admin'] + $ringkasan['pelapor'];

        return view('superadmin.accounts.index', compact('users', 'ringkasan'));
    }

    /**
     * Halaman form tambah akun.
     */
    public function create()
    {
        return view('superadmin.accounts.create', [
            'daftarRole' => self::ROLE_TERMURAH,
        ]);
    }

    /**
     * Simpan akun baru.
     * Super admin hanya boleh membuat admin atau pelapor.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'role' => [
                'required',
                Rule::in(self::ROLE_TERMURAH),
            ],

            'nama' => [
                'required',
                'string',
                'min:3',
                'max:255',
            ],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
            ],

            'posisi' => [
                'nullable',
                'string',
                'max:255',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ], [], [
            'role' => 'jabatan',
            'nama' => 'nama',
            'email' => 'email',
            'posisi' => 'unit kerja',
            'password' => 'kata sandi',
        ]);

        $this->pastikanEmailBisaDipakai($validated['email']);

        User::withRole($validated['role'], [
            'name' => trim($validated['nama']),
            'email' => mb_strtolower($validated['email']),
            'password' => $validated['password'],
            'posisi' => $validated['posisi'] ?? null,
        ]);

        return redirect()
            ->route('superadmin.accounts.index')
            ->with('success', 'Akun berhasil ditambahkan.');
    }

    /**
     * Halaman edit akun.
     */
    public function edit(User $account)
    {
        $this->ensureManageableAccount($account);

        return view('superadmin.accounts.edit', [
            'account' => $account,
            'daftarRole' => self::ROLE_TERMURAH,
        ]);
    }

    /**
     * Update akun.
     * type profile  = update nama, email, jabatan, role.
     * type password = update password saja.
     */
    public function update(Request $request, User $account)
    {
        $this->ensureManageableAccount($account);

        $type = $request->input('type');

        if ($type === 'password') {
            $validated = $request->validate([
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'confirmed',
                ],
            ], [], [
                'password' => 'kata sandi',
            ]);

            $account->update([
                'password' => $validated['password'],
            ]);

            return redirect()
                ->route('superadmin.accounts.index')
                ->with('success', 'Password akun berhasil diperbarui.');
        }

        $validated = $request->validate([
            'role' => [
                'required',
                Rule::in(self::ROLE_TERMURAH),
            ],

            'nama' => [
                'required',
                'string',
                'min:3',
                'max:255',
            ],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($account->id),
            ],

            'posisi' => [
                'nullable',
                'string',
                'max:255',
            ],
        ], [], [
            'role' => 'jabatan',
            'nama' => 'nama',
            'email' => 'email',
            'posisi' => 'unit kerja',
        ]);

        $this->pastikanEmailBisaDipakai($validated['email'], $account->id);

        $account->update([
            'name' => trim($validated['nama']),
            'email' => mb_strtolower($validated['email']),
            'posisi' => $validated['posisi'] ?? null,
        ]);

        // Role lewat forceFill, bukan mass assignment.
        $account->forceFill(['role' => $validated['role']])->save();

        return redirect()
            ->route('superadmin.accounts.index')
            ->with('success', 'Profil akun berhasil diperbarui.');
    }

    /**
     * Nonaktifkan akun.
     *
     * Soft delete, bukan hard delete: laporan dan riwayat status milik
     * pelapor adalah data aduan publik yang tidak boleh ikut terhapus
     * karena akunnya dinonaktifkan. Data tetap bisa dipulihkan lewat
     * `restore()`.
     */
    public function destroy(User $account)
    {
        $this->ensureManageableAccount($account);

        $jumlahLaporan = $account->reports()->count();
        $jumlahRiwayat = $account->statuses()->count();

        $account->forceFill(['is_active' => false])->save();
        $account->delete();

        return redirect()
            ->route('superadmin.accounts.index')
            ->with('success', sprintf(
                'Akun %s dinonaktifkan. %d laporan dan %d riwayat status tetap tersimpan dan bisa dipulihkan.',
                $account->email,
                $jumlahLaporan,
                $jumlahRiwayat
            ));
    }

    /**
     * Aktifkan kembali akun yang dinonaktifkan.
     */
    public function restore(User $account)
    {
        $this->ensureManageableAccount($account);

        $email = $account->email;

        $tabrakan = User::withTrashed()
            ->where('email', $email)
            ->whereKeyNot($account->id)
            ->exists();

        if ($tabrakan) {
            return back()->with('error', 'Email tersebut sudah dipakai akun lain, akun tidak bisa diaktifkan.');
        }

        $account->restore();
        $account->forceFill(['is_active' => true])->save();

        return redirect()
            ->route('superadmin.accounts.index')
            ->with('success', 'Akun berhasil diaktifkan kembali.');
    }

    /**
     * Keamanan tambahan:
     * hanya akun admin dan pelapor yang boleh dikelola dari halaman ini.
     * super_admin tidak boleh tampil, diedit, atau dihapus lewat fitur ini.
     */
    private function ensureManageableAccount(User $account): void
    {
        if (! in_array($account->role, self::ROLE_TERMURAH, true)) {
            abort(404);
        }
    }

    /**
     * Soft delete melepas batasan unique pada kolom email, jadi email
     * milik akun nonaktif bisa dipakai akun lain. Kalau akun nonaktif
     * itu nanti dipulihkan, emailnya harus kembali milik dia.
     */
    private function pastikanEmailBisaDipakai(string $email, ?int $kecualiId = null): void
    {
        $email = mb_strtolower($email);

        $tabrakan = User::withTrashed()
            ->where('email', $email)
            ->when($kecualiId, fn ($q) => $q->whereKeyNot($kecualiId))
            ->exists();

        if (! $tabrakan) {
            return;
        }

        throw ValidationException::withMessages([
            'email' => 'Email ini masih dipakai akun yang dinonaktifkan. Aktifkan kembali akun tersebut, atau ganti email yang dipakai.',
        ]);
    }
}

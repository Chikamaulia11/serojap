<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    /**
     * Halaman daftar akun admin dan pelapor.
     */
    public function index()
    {
        $users = User::query()
            ->whereIn('role', ['admin', 'pelapor'])
            ->orderByDesc('id')
            ->get();

        $admins = $users->where('role', 'admin');

        $pelapors = $users->where('role', 'pelapor');

        return view('superadmin.accounts.index', compact(
            'users',
            'admins',
            'pelapors'
        ));
    }

    /**
     * Halaman form tambah akun.
     */
    public function create()
    {
        return view('superadmin.accounts.create');
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
                Rule::in(['admin', 'pelapor']),
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
                'unique:users,email',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        User::create([
            'name' => $validated['nama'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
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

        return view('superadmin.accounts.edit', compact('account'));
    }

    /**
     * Update akun.
     * type profile  = update nama, email, role.
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

                'password_confirmation' => [
                    'required',
                    'string',
                    'min:8',
                ],
            ]);

            $account->update([
                'password' => Hash::make($validated['password']),
            ]);

            return redirect()
                ->route('superadmin.accounts.index')
                ->with('success', 'Password akun berhasil diperbarui.');
        }

        $validated = $request->validate([
            'role' => [
                'required',
                Rule::in(['admin', 'pelapor']),
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
        ]);

        $account->update([
            'name' => $validated['nama'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ]);

        return redirect()
            ->route('superadmin.accounts.index')
            ->with('success', 'Profil akun berhasil diperbarui.');
    }

    /**
     * Hapus akun admin atau pelapor.
     */
    public function destroy(User $account)
    {
        $this->ensureManageableAccount($account);

        $account->delete();

        return redirect()
            ->route('superadmin.accounts.index')
            ->with('success', 'Akun berhasil dihapus.');
    }

    /**
     * Keamanan tambahan:
     * hanya akun admin dan pelapor yang boleh dikelola dari halaman ini.
     * super_admin tidak boleh tampil, diedit, atau dihapus lewat fitur ini.
     */
    private function ensureManageableAccount(User $account): void
    {
        if (! in_array($account->role, ['admin', 'pelapor'], true)) {
            abort(404);
        }
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateAdminRequest;
use App\Http\Requests\Admin\UpdateAdminRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminAccountController extends Controller
{
    // Semua endpoint halaman ini dijaga oleh SuperAdminMiddleware di routes.

    public function index()
    {
        $admins = User::query()
            ->where('role', 'admin')
            ->orderByDesc('id')
            ->get();

        $pelapors = User::query()
            ->where('role', 'pelapor')
            ->orderByDesc('id')
            ->get();

        $users = User::query()
            ->whereIn('role', ['admin', 'pelapor'])
            ->orderByDesc('id')
            ->get();

        return view(
            'admin.admin-accounts.index',
            compact('admins', 'pelapors', 'users')
        );
    }

    public function create()
    {
        return view('admin.admin-accounts.create');
    }

    public function store(CreateAdminRequest $request)
    {
        $role = $request->input('role');

        User::create([
            'name' => $request->input('nama'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role' => $role,
            'posisi' => $role === 'admin'
                ? $request->input('posisi')
                : null,
        ]);

        $message = $role === 'admin'
            ? 'Akun admin berhasil ditambahkan.'
            : 'Akun pelapor berhasil ditambahkan.';

        return redirect()
            ->route('admin.admin-accounts.index')
            ->with('success', $message);
    }

    public function edit(User $admin)
    {
        // Route param tetap {admin}, tapi data yang dikelola bisa admin atau pelapor.
        if (! in_array($admin->role, ['admin', 'pelapor'])) {
            abort(404);
        }

        return view('admin.admin-accounts.edit', [
            'account' => $admin,
        ]);
    }

    public function update(UpdateAdminRequest $request, User $admin)
    {
        if (! in_array($admin->role, ['admin', 'pelapor'])) {
            abort(404);
        }

        $type = $request->input('type');

        if ($type === 'password') {
            $admin->update([
                'password' => Hash::make($request->input('password')),
            ]);

            return redirect()
                ->route('admin.admin-accounts.index')
                ->with('success', 'Password akun berhasil diperbarui.');
        }

        $role = $request->input('role');

        $admin->update([
            'name' => $request->input('nama'),
            'email' => $request->input('email'),
            'role' => $role,
            'posisi' => $role === 'admin'
                ? $request->input('posisi')
                : null,
        ]);

        return redirect()
            ->route('admin.admin-accounts.index')
            ->with('success', 'Data akun berhasil diperbarui.');
    }

    public function destroy(User $admin)
    {
        if (! in_array($admin->role, ['admin', 'pelapor'])) {
            abort(404);
        }

        $role = $admin->role;

        $admin->delete();

        $message = $role === 'admin'
            ? 'Akun admin berhasil dihapus.'
            : 'Akun pelapor berhasil dihapus.';

        return redirect()
            ->route('admin.admin-accounts.index')
            ->with('success', $message);
    }
}
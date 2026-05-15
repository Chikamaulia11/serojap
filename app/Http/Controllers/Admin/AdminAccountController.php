<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateAdminRequest;
use App\Http\Requests\Admin\UpdateAdminRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminAccountController extends Controller
{
    // NOTE: Semua endpoint di halaman ini dijaga oleh SuperAdminMiddleware di routes.

    public function index()
    {
        $admins = User::query()
            ->where('role', 'admin')
            ->orderByDesc('id')
            ->get();

        return view('admin.admin-accounts.index', compact('admins'));
    }

    public function create()
    {
        return view('admin.admin-accounts.create');
    }

    public function store(CreateAdminRequest $request)
    {
        User::create([
            'name' => $request->input('nama'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role' => 'admin',
            'posisi' => $request->input('posisi'),
        ]);

        return redirect()
            ->route('admin.admin-accounts.index')
            ->with('success', 'Admin berhasil ditambahkan.');
    }

    public function edit(User $admin)
    {
        // Pastikan hanya admin role admin yang bisa di-edit
        if ($admin->role !== 'admin') {
            abort(404);
        }

        return view('admin.admin-accounts.edit', compact('admin'));
    }

    public function update(UpdateAdminRequest $request, User $admin)
    {
        if ($admin->role !== 'admin') {
            abort(404);
        }

        $admin->update([
            'name' => $request->input('nama'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'posisi' => $request->input('posisi'),
        ]);

        return redirect()
            ->route('admin.admin-accounts.index')
            ->with('success', 'Admin berhasil diperbarui.');
    }

    public function destroy(User $admin)
    {
        if ($admin->role !== 'admin') {
            abort(404);
        }

        $admin->delete();

        return redirect()
            ->route('admin.admin-accounts.index')
            ->with('success', 'Admin berhasil dihapus.');
    }
}




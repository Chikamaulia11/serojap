<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Menampilkan halaman login untuk PELAPOR (User Biasa)
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Menampilkan halaman login untuk ADMIN & SUPER ADMIN
     */
    public function createAdmin(): View
    {
        // Pastikan kamu sudah buat file: resources/views/auth/login-admin.blade.php
        return view('auth.login-admin');
    }

    /**
     * Proses Login
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();

        // --- LOGIKA REDIRECT BERDASARKAN ROLE ---
        if ($user->role === 'admin' || $user->role === 'super_admin') {
            return redirect()->intended(route('admin.dashboard', absolute: false));
        }
        // ----------------------------------------

        // Jika user biasa/pelapor, lempar ke dashboard utama
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Proses Logout
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
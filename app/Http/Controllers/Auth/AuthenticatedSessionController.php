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
    // =========================
    // HALAMAN LOGIN USER
    // =========================
    public function create(): View
    {
        return view('auth.login');
    }

    // =========================
    // HALAMAN LOGIN ADMIN
    // =========================
    public function createAdmin(): View
    {
        return view('auth.login-admin');
    }

    // =========================
    // PROSES LOGIN
    // =========================
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        // =========================
        // REDIRECT ADMIN
        // =========================
        if (
            $user->role === 'admin' ||
            $user->role === 'super_admin'
        ) {

            return redirect()
                ->route('admin.dashboard');

        }

        // =========================
        // REDIRECT USER / PELAPOR
        // =========================
        return redirect()
            ->route('dashboard');
    }

    // =========================
    // LOGOUT
    // =========================
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
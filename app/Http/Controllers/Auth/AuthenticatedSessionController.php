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
    // HALAMAN LOGIN USER / PELAPOR
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
    // HALAMAN LOGIN SUPER ADMIN
    // =========================
    public function createSuperAdmin(): View
    {
        return view('auth.login-superadmin');
    }

    // =========================
    // PROSES LOGIN
    // =========================
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        $routeName = $request->route()?->getName();

        // =========================
        // LOGIN PELAPOR
        // =========================
        if ($routeName === 'login.post' || $routeName === 'login') {

            if ($user->role !== 'pelapor') {
                return $this->logoutAndBack(
                    $request,
                    'Akun ini bukan akun pelapor.'
                );
            }

            return redirect()
                ->route('dashboard');
        }

        // =========================
        // LOGIN ADMIN
        // =========================
        if ($routeName === 'login.admin.post') {

            if ($user->role !== 'admin') {
                return $this->logoutAndBack(
                    $request,
                    'Akun ini bukan akun admin.'
                );
            }

            return redirect()
                ->route('admin.dashboard');
        }

        // =========================
        // LOGIN SUPER ADMIN
        // =========================
        if ($routeName === 'login.superadmin.post') {

            if ($user->role !== 'super_admin') {
                return $this->logoutAndBack(
                    $request,
                    'Akun ini bukan akun super admin.'
                );
            }

            return redirect()
                ->route('superadmin.dashboard');
        }

        // =========================
        // FALLBACK BERDASARKAN ROLE
        // =========================
        if ($user->role === 'super_admin') {
            return redirect()
                ->route('superadmin.dashboard');
        }

        if ($user->role === 'admin') {
            return redirect()
                ->route('admin.dashboard');
        }

        return redirect()
            ->route('dashboard');
    }

    // =========================
    // LOGOUT DAN KEMBALI KE LOGIN
    // =========================
    private function logoutAndBack(Request $request, string $message): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => $message,
            ]);
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
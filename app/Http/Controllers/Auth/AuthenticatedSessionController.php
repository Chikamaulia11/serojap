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
     * =========================
     * PETA LOGIN -> ROLE
     *
     * Route login (berdasarkan nama route) → role yang diizinkan,
     * tujuan dashboard, dan pesan penolakan.
     * Menambah role baru cukup dilakukan di sini.
     * =========================
     */
    private const PETA_LOGIN = [
        'login' => [
            'role' => 'pelapor',
            'dashboard' => 'dashboard',
            'pesan' => 'Akun ini bukan akun pelapor.',
        ],
        'login.post' => [
            'role' => 'pelapor',
            'dashboard' => 'dashboard',
            'pesan' => 'Akun ini bukan akun pelapor.',
        ],
        'login.admin.post' => [
            'role' => 'admin',
            'dashboard' => 'admin.dashboard',
            'pesan' => 'Akun ini bukan akun admin.',
        ],
        'login.superadmin.post' => [
            'role' => 'super_admin',
            'dashboard' => 'superadmin.dashboard',
            'pesan' => 'Akun ini bukan akun super admin.',
        ],
    ];

    /**
     * =========================
     * PETA ROLE -> DASHBOARD
     *
     * Dipakai kalau login datang dari route yang tidak punya aturan
     * khusus, jadi user diarahkan ke dashboard sesuai role-nya.
     * =========================
     */
    private const PETA_DASHBOARD = [
        'pelapor' => 'dashboard',
        'admin' => 'admin.dashboard',
        'super_admin' => 'superadmin.dashboard',
    ];

    /**
     * Dashboard default bila role tidak dikenal.
     */
    private const DASHBOARD_DEFAULT = 'dashboard';

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

    /**
     * =========================
     * PROSES LOGIN
     * =========================
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        $aturan = self::PETA_LOGIN[$request->route()?->getName()] ?? null;

        // =========================
        // CEK ROLE SESUAI HALAMAN LOGIN
        // =========================
        if ($aturan !== null) {
            if (! $user->hasRole($aturan['role'])) {
                return $this->logoutAndBack($request, $aturan['pesan']);
            }

            return redirect()->route($aturan['dashboard']);
        }

        // =========================
        // FALLBACK BERDASARKAN ROLE
        // =========================
        return redirect()->route(
            self::PETA_DASHBOARD[$user->role] ?? self::DASHBOARD_DEFAULT
        );
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

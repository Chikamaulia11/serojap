<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {

        // =========================
        // BELUM LOGIN
        // =========================
        if (! Auth::check()) {
            return redirect()
                ->route('login.admin')
                ->withErrors([
                    'email' => 'Silakan login sebagai admin untuk melanjutkan.',
                ]);
        }

        $user = Auth::user();

        // =========================
        // AKUN DINONAKTIFKAN
        // =========================
        if (! $user->bisaLogin()) {
            $this->keluarkan($request);

            return redirect()
                ->route('login.admin')
                ->withErrors([
                    'email' => 'Akun Anda sudah dinonaktifkan oleh super admin. Hubungi super admin untuk mengaktifkan kembali.',
                ]);
        }

        // =========================
        // SUPER ADMIN DI AREA ADMIN
        // ARAHKAN KE DASHBOARD SUPER ADMIN
        // =========================
        if ($user->isSuperAdmin()) {
            return redirect()->route('superadmin.dashboard');
        }

        // =========================
        // ADMIN BIASA SAJA
        // =========================
        if ($user->isAdmin()) {
            return $next($request);
        }

        // =========================
        // ROLE LAIN (PELAPOR)
        // Jangan logout: user-nya cuma salah klik, sesi tetap dipakai.
        // =========================
        if ($user->isPelapor()) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Halaman admin hanya untuk petugas. Kamu sedang masuk sebagai pelapor.');
        }

        return redirect()
            ->route('login.admin')
            ->withErrors([
                'email' => 'Anda tidak memiliki akses ke dashboard admin.',
            ]);
    }

    private function keluarkan(Request $request): void
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}

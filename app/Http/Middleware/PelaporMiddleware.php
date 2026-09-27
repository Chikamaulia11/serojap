<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PelaporMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(
        Request $request,
        Closure $next
    ): Response {

        // =========================
        // BELUM LOGIN
        // =========================
        if (! Auth::check()) {
            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Silakan login terlebih dahulu untuk melanjutkan.',
                ]);
        }

        $user = Auth::user();

        // =========================
        // AKUN DINONAKTIFKAN
        // =========================
        if (! $user->bisaLogin()) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Akun Anda sudah dinonaktifkan oleh super admin. Hubungi super admin untuk mengaktifkan kembali.',
                ]);
        }

        // =========================
        // PELAPOR
        // =========================
        if ($user->isPelapor()) {
            return $next($request);
        }

        // =========================
        // ROLE LAIN
        // Arahkan ke dashboard sesuai role-nya, jangan logout.
        // =========================
        if ($user->isSuperAdmin()) {
            return redirect()->route('superadmin.dashboard');
        }

        if ($user->isAdmin()) {
            return redirect()
                ->route('admin.dashboard')
                ->with('error', 'Halaman pelapor hanya untuk warga. Kamu sedang masuk sebagai petugas.');
        }

        return redirect()
            ->route('login')
            ->withErrors([
                'email' => 'Anda tidak memiliki akses ke dashboard pelapor.',
            ]);
    }
}

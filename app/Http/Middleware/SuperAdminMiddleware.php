<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()
                ->route('login.superadmin')
                ->withErrors([
                    'email' => 'Silakan login sebagai super admin untuk melanjutkan.',
                ]);
        }

        $user = Auth::user();

        if (! $user->bisaLogin()) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login.superadmin')
                ->withErrors([
                    'email' => 'Akun Anda sudah dinonaktifkan. Hubungi super admin lain untuk mengaktifkan kembali.',
                ]);
        }

        if (! $user->isSuperAdmin()) {
            if ($user->isAdmin()) {
                return redirect()
                    ->route('admin.dashboard')
                    ->with('error', 'Halaman super admin hanya untuk super admin.');
            }

            return redirect()
                ->route('dashboard')
                ->with('error', 'Halaman super admin hanya untuk super admin.');
        }

        return $next($request);
    }
}

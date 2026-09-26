<?php

namespace App\Http\Middleware;

use App\Models\User;
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
        // ADMIN BIASA SAJA
        // SUPER ADMIN SUDAH DIPISAH
        // =========================
        if (
            Auth::check()
            && Auth::user()->hasRole(User::ROLE['admin'])
        ) {
            return $next($request);
        }

        // =========================
        // JIKA SUPER ADMIN MASUK KE AREA ADMIN
        // ARAHKAN KE DASHBOARD SUPER ADMIN
        // =========================
        if (
            Auth::check()
            && Auth::user()->hasRole(User::ROLE['super_admin'])
        ) {
            return redirect()
                ->route('superadmin.dashboard');
        }

        // =========================
        // JIKA BUKAN ADMIN
        // =========================
        if (Auth::check()) {

            Auth::logout();

            $request->session()->invalidate();

            $request->session()->regenerateToken();

        }

        return redirect()
            ->route('login.admin')
            ->withErrors([
                'email' => 'Anda tidak memiliki akses ke dashboard admin.'
            ]);
    }
}
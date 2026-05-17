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
        // CEK LOGIN
        // =========================
        if (
            Auth::check()
            && (
                Auth::user()->role === 'admin'
                || Auth::user()->role === 'super_admin'
            )
        ) {

            return $next($request);

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
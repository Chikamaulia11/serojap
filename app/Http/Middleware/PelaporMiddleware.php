<?php

namespace App\Http\Middleware;

use App\Models\User;
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
        // CEK ROLE PELAPOR
        // =========================
        if (
            Auth::check()
            && Auth::user()->hasRole(User::ROLE['pelapor'])
        ) {

            return $next($request);

        }

        // =========================
        // JIKA BUKAN PELAPOR
        // =========================
        if (Auth::check()) {

            Auth::logout();

            $request->session()->invalidate();

            $request->session()->regenerateToken();

        }

        return redirect()
            ->route('login')
            ->withErrors([
                'email' => 'Anda tidak memiliki akses ke dashboard pelapor.'
            ]);
    }
}
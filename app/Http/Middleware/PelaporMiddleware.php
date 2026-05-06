<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
<<<<<<< HEAD
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PelaporMiddleware
{
    /**
     * Handle an incoming request.
     */
=======
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class PelaporMiddleware
{
>>>>>>> b793b33 (backup sebelum merge)
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->role === 'pelapor') {
            return $next($request);
        }
<<<<<<< HEAD
        
        if (Auth::check()) {
            // Logout jika bukan pelapor
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect()->route('login')
            ->withErrors(['email' => 'Anda tidak memiliki akses ke dashboard pelapor.']);
    }
}
=======

        abort(403, 'Akses ditolak, bukan pelapor');
    }
}
>>>>>>> b793b33 (backup sebelum merge)

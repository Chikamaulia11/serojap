<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RolePelapor
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Belum login → arahkan ke login
        if (! $user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Bukan pelapor → blok akses
        if (! $user->hasRole(User::ROLE['pelapor'])) {
            abort(403, 'Akses hanya untuk pelapor');
        }

        return $next($request);
    }
}
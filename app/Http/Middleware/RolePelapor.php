<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RolePelapor
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Belum login → arahkan ke login
        if ($user->role !== 'pelapor') {
    return redirect('/login')->with('error', 'Akses ditolak');
}
        // Bukan pelapor → blok akses
        if ($user->role !== 'pelapor') {
            abort(403, 'Akses hanya untuk pelapor');
        }

        return $next($request);
    }
}
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Cek apakah user sudah login atau belum
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // 2. Ambil role user yang sedang login
        $role = Auth::user()->role;

        // 3. Izinkan lewat jika rolenya 'admin' atau 'super_admin'
        if ($role === 'admin' || $role === 'super_admin') {
            return $next($request);
        }

        // 4. Jika bukan admin (alias user biasa), lempar ke dashboard user
        return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses admin.');
    }
}

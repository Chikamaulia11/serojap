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
                ->route('login.admin')
                ->withErrors([
                    'email' => 'Anda harus login sebagai admin.'
                ]);
        }

        if (Auth::user()->role !== 'super_admin') {
            // Amankan agar pengguna admin biasa tidak bisa akses aksi super admin.
            return redirect()
                ->route('admin.dashboard')
                ->withErrors([
                    'email' => 'Anda tidak memiliki akses sebagai super admin.'
                ]);
        }


        return $next($request);
    }
}


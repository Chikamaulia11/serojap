<?php

namespace App\Http\Middleware;

use App\Models\User;
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
                    'email' => 'Anda harus login sebagai super admin.'
                ]);
        }

        if (! Auth::user()->hasRole(User::ROLE['super_admin'])) {

            if (Auth::user()->hasRole(User::ROLE['admin'])) {
                return redirect()
                    ->route('admin.dashboard')
                    ->withErrors([
                        'email' => 'Anda tidak memiliki akses sebagai super admin.'
                    ]);
            }

            return redirect()
                ->route('login.superadmin')
                ->withErrors([
                    'email' => 'Anda tidak memiliki akses sebagai super admin.'
                ]);
        }

        return $next($request);
    }
}
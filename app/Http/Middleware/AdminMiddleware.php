<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
{
    if (Auth::check() && (Auth::user()->role == 'admin' || Auth::user()->role == 'super_admin')) {
        return $next($request);
    }
    
    if (Auth::check()) {
        $user = Auth::user();
        $user->setRememberToken(null);
        $user->save();
        
        Auth::guard('web')->logout();
    }

    return redirect()->route('login.admin')->withErrors([
        'email' => 'Sesi ditutup karena Anda bukan Administrator.'
    ]);
}

}
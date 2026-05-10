<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;

class AuthenticatedSessionController extends Controller
{
    public function create(): View 
    { 
        return view('auth.login'); 
    }

    public function createAdmin(): View 
    { 
        return view('auth.login-admin'); 
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        // ADMIN
        if ($user->role === 'admin' || $user->role === 'super_admin') {
            return redirect('/admin/dashboard');
        }

        // PELAPOR
        return redirect()->route('dashboard');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
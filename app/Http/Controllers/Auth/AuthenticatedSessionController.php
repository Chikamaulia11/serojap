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
    /**
     * Tampilan Login Pelapor
     */
    public function create(): View 
    { 
        return view('auth.login'); 
    }

    /**
     * Tampilan Login Admin
     */
    public function createAdmin(): View 
    { 
        return view('auth.login-admin'); 
    }

    /**
     * Proses Login (Satu Pintu untuk Semua)
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        if ($request->routeIs('login.admin.post')) {
            $user = User::where('email', $request->email)->first();

            if ($user && !in_array($user->role, ['admin', 'super_admin'])) {
                return redirect()->route('login.admin')
                    ->withErrors(['email' => 'Akses Ditolak! Akun Anda tidak terdaftar sebagai Administrator.']);
            }
        }

        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->role === 'admin' || $user->role === 'super_admin') {
            return redirect()->route('admin.dashboard');
        }
        
        return redirect()->route('dashboard');
    }

    /**
     * Proses Logout
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
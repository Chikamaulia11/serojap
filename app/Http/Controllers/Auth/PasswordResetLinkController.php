<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Halaman "Lupa kata sandi".
     *
     * Sebelumnya route `password.request` tidak pernah didaftarkan,
     * sehingga blok "Lupa kata sandi?" di halaman login (yang hanya
     * dirender kalau `Route::has('password.request')`) tidak muncul
     * sama sekali. Warga yang lupa kata sandi tidak punya jalan lain
     * selain mendaftar ulang akun.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
        ], [], [
            'email' => 'email',
        ]);

        $status = Password::sendResetLink([
            'email' => mb_strtolower(trim($request->email)),
        ]);

        // Pesan sengaja sama untuk email terdaftar maupun tidak supaya
        // halaman ini tidak bisa dipakai menebak email yang punya akun.
        $pesanBerhasil = 'Kalau email itu terdaftar, kami sudah mengirim tautan untuk mengatur ulang kata sandi. Silakan cek folder spam juga.';

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', $pesanBerhasil);
        }

        if ($status === Password::INVALID_USER) {
            return back()->with('status', $pesanBerhasil);
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => trans($status)]);
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $email = mb_strtolower(trim((string) $request->input('email')));

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', $this->emailBelumDipakai($email)],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [], [
            'name' => 'nama',
            'email' => 'email',
            'password' => 'kata sandi',
        ]);

        $user = User::withRole(User::ROLE['pelapor'], [
            'name' => trim($request->name),
            'email' => $email,
            'password' => $request->password,
        ]);

        event(new Registered($user));

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Registrasi berhasil. Selamat datang di SEROJAP!');
    }

    /**
     * Aturan unik email yang ikut memperhitungkan akun yang sudah
     * di-soft-delete.
     *
     * Dua hal bertemu di sini dan hasilnya membingungkan:
     *
     * 1. Kolom `email` tidak lagi punya unique global. Yang ada hanya
     *    `unique(['email', 'deleted_at'])` supaya satu email bisa
     *    dipakai lagi setelah akunnya dinonaktifkan.
     *
     * 2. `Rule::unique(User::class)` menghormati global scope
     *    SoftDeletes, jadi ia hanya melihat akun yang BELUM dihapus.
     *
     * Akibatnya `Rule::unique()` biasa menerima email milik akun yang
     * sudah dinonaktifkan, dan baris baru itu tersimpan dengan
     * `deleted_at = NULL`. Di MySQL `NULL` pada unique index tidak
     * dianggap duplikat, jadi database pun tidak menolaknya.
     *
     * Efek yang terjadi pada pengguna: daftar sukses, tapi
     * setiap percobaan login berikutnya ditolak sebagai "akun
     * dinonaktifkan", karena `Auth::attempt()` menemukan akun lama
     * lebih dulu -- bukan akun yang baru dibuat.
     *
     * Di sini email diperiksa dengan `withTrashed()`, jadi email milik
     * akun nonaktif ditolak sejak awal dengan pesan yang mengarahkan
     * ke super admin, sesuai alur pemulihan akun yang sudah dipakai di
     * halaman login.
     */
    private function emailBelumDipakai(string $email): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail) use ($email): void {
            if (! $email) {
                return;
            }

            $ada = User::withTrashed()
                ->whereRaw('LOWER(email) = ?', [$email])
                ->first();

            if (! $ada) {
                return;
            }

            $fail($ada->trashed()
                ? 'Email ini pernah dipakai akun yang sudah dinonaktifkan. Hubungi super admin untuk mengaktifkan kembali, jangan daftar ulang.'
                : 'Email ini sudah terdaftar. Silakan masuk.');
        };
    }
}

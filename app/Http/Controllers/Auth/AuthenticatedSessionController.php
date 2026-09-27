<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * =========================
     * PETA LOGIN -> ROLE
     *
     * Route login (berdasarkan nama route) → role yang diizinkan,
     * tujuan dashboard, dan pesan penolakan.
     * Menambah role baru cukup dilakukan di sini.
     * =========================
     */
    private const PETA_LOGIN = [
        'login' => [
            'role' => 'pelapor',
            'dashboard' => 'dashboard',
            'halaman' => 'login',
            'pesan' => 'Akun ini bukan akun pelapor.',
        ],
        'login.post' => [
            'role' => 'pelapor',
            'dashboard' => 'dashboard',
            'halaman' => 'login',
            'pesan' => 'Akun ini bukan akun pelapor.',
        ],
        'login.admin.post' => [
            'role' => 'admin',
            'dashboard' => 'admin.dashboard',
            'halaman' => 'login.admin',
            'pesan' => 'Akun ini bukan akun admin.',
        ],
        'login.superadmin.post' => [
            'role' => 'super_admin',
            'dashboard' => 'superadmin.dashboard',
            'halaman' => 'login.superadmin',
            'pesan' => 'Akun ini bukan akun super admin.',
        ],
    ];

    /**
     * =========================
     * PETA ROLE -> DASHBOARD
     *
     * Dipakai kalau login datang dari route yang tidak punya aturan
     * khusus, jadi user diarahkan ke dashboard sesuai role-nya.
     * =========================
     */
    private const PETA_DASHBOARD = [
        'pelapor' => 'dashboard',
        'admin' => 'admin.dashboard',
        'super_admin' => 'superadmin.dashboard',
    ];

    /**
     * Dashboard default bila role tidak dikenal.
     */
    private const DASHBOARD_DEFAULT = 'dashboard';

    // =========================
    // HALAMAN LOGIN USER / PELAPOR
    // =========================
    public function create(): View
    {
        return view('auth.login');
    }

    // =========================
    // HALAMAN LOGIN ADMIN
    // =========================
    public function createAdmin(): View
    {
        return view('auth.login-admin');
    }

    // =========================
    // HALAMAN LOGIN SUPER ADMIN
    // =========================
    public function createSuperAdmin(): View
    {
        return view('auth.login-superadmin');
    }

    /**
     * =========================
     * PROSES LOGIN
     * =========================
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        $aturan = self::PETA_LOGIN[$request->route()?->getName()] ?? null;

        // =========================
        // CEK ROLE SESUAI HALAMAN LOGIN
        // =========================
        if ($aturan !== null) {
            if (! $user->hasRole($aturan['role'])) {
                return $this->logoutAndBack($request, $aturan['halaman'], $aturan['pesan']);
            }

            return redirect()->route($aturan['dashboard']);
        }

        // =========================
        // FALLBACK BERDASARKAN ROLE
        // =========================
        return redirect()->route(
            self::PETA_DASHBOARD[$user->role] ?? self::DASHBOARD_DEFAULT
        );
    }

    // =========================
    // LOGOUT DAN KEMBALI KE LOGIN
    // =========================
    private function logoutAndBack(Request $request, string $halaman, string $message): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // Pesan harus lewat `withErrors(['email' => ...])`, bukan
        // `->with('error', ...)`.
        //
        // Alasannya: ketiga halaman login (/login, /login/admin,
        // /login/superadmin) menampilkan error lewat
        // `<x-input-error :messages="$errors->get('email')" />`, dan
        // `x-auth-session-status` membaca `session('status')`.
        // TIDAK ADA satu pun yang membaca `session('error')` di halaman
        // login -- key itu hanya dibaca `layouts/app.blade.php` dan
        // beberapa view admin/superadmin.
        //
        // Jadi versi lama: orang yang salah klik "/login/admin" lalu
        // dikembalikan dengan diam, tanpa penjelasan sama sekali.
        //
        // PENTING: jangan pakai `back()` di sini. `invalidate()` di atas
        // melakukan `flush()`, jadi key `_previous.url` ikut terhapus --
        // `back()` lalu jatuh ke fallback "/" yang tidak menampilkan
        // pesan apa pun, dan hasilnya jadi tidak konsisten: browser yang
        // mengirim header `Referer` kena, yang tidak tidak. Karena area
        // login-nya sudah diketahui dari `PETA_LOGIN`, redirect eksplisit
        // saja.
        return redirect()
            ->route($halaman)
            ->withInput($request->only('email'))
            ->withErrors(['email' => $message]);
    }

    // =========================
    // LOGOUT
    // =========================
    public function destroy(Request $request): RedirectResponse
    {
        // Dibaca sebelum session di-invalidate: `redirect` datang dari
        // body POST, bukan dari session, tapi membaca lebih dulu membuat
        // urutannya jelas.
        $tujuan = $this->tujuanSetelahLogout($request);

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect($tujuan);
    }

    /**
     * Ke mana pengguna dikirim setelah logout.
     *
     * Halaman 403 memuat tombol "Ganti Akun": logout dulu, baru
     * tampilkan halaman login area mereka. Kalau tujuan tidak dikenal,
     * tetap beranda.
     *
     * Input `redirect` tidak dipercaya buta. Kalau diteruskan apa
     * adanya, `POST /logout?redirect=https://phishing.example` jadi
     * open redirect: link logout yangownersk sendirian bisa mengirim
     * orang ke domain lain. Karena itu hanya path halaman login milik
     * aplikasi sendiri yang diterima, dan sisanya jatuh ke beranda.
     */
    private function tujuanSetelahLogout(Request $request): string
    {
        $diminta = $request->input('redirect');

        if (! is_string($diminta) || $diminta === '') {
            return '/';
        }

        $halamanLogin = [
            route('login'),
            route('login.admin'),
            route('login.superadmin'),
        ];

        return in_array($diminta, $halamanLogin, true) ? $diminta : '/';
    }
}

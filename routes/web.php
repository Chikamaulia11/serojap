<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\ReportController;

use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\TabelFaqController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\StatistikController;
use App\Http\Controllers\Admin\AdminProfileController;

use App\Http\Controllers\SuperAdmin\AccountController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;

use App\Http\Controllers\Pelapor\FaqController;

use App\Http\Middleware\SuperAdminMiddleware;

/* =========================
   LANDING PAGE
========================= */
Route::get(
    '/',
    [PublicController::class, 'home']
)->name('beranda');

/* =========================
   AUTH (GUEST)
========================= */
Route::middleware('guest')->group(function () {

    // =========================
    // LOGIN PELAPOR
    // =========================
    Route::get(
        '/login',
        [AuthenticatedSessionController::class, 'create']
    )->name('login');

    Route::post(
        '/login',
        [AuthenticatedSessionController::class, 'store']
    )->name('login.post');

    // =========================
    // LOGIN ADMIN
    // =========================
    Route::get(
        '/login/admin',
        [AuthenticatedSessionController::class, 'createAdmin']
    )->name('login.admin');

    Route::post(
        '/login/admin',
        [AuthenticatedSessionController::class, 'store']
    )->name('login.admin.post');

    // =========================
    // LOGIN SUPER ADMIN
    // =========================
    Route::get(
        '/login/superadmin',
        [AuthenticatedSessionController::class, 'createSuperAdmin']
    )->name('login.superadmin');

    Route::post(
        '/login/superadmin',
        [AuthenticatedSessionController::class, 'store']
    )->name('login.superadmin.post');

});

/* =========================
   PROTECTED (PELAPOR)
========================= */
Route::middleware([
    'auth',
    'pelapor'
])->group(function () {

    Route::get(
        '/dashboard',
        [DashboardController::class, 'index']
    )->name('dashboard');

    Route::get(
        '/report',
        [ReportController::class, 'create']
    )->name('laporan.create');

    Route::post(
        '/report',
        [ReportController::class, 'store']
    )
        // Batasi spam submit laporan, tidak memengaruhi navigasi halaman lain.
        // Hitunganattempt dilakukan di controller agar percobaan gagal
        // validasi tidak ikut menghabiskan kuota.
        ->middleware('throttle:20,1')
        ->name('laporan.store');

    Route::get(
        '/my-report',
        [ReportController::class, 'myReport']
    )->name('laporan.my-report');

    Route::get(
        '/pusat-bantuan',
        [FaqController::class, 'index']
    )->name('pelapor.faq');

    Route::get('/prosedur', function () {
        return view('pelapor.prosedur');
    })->name('prosedur');

    Route::get(
        '/profile',
        [ProfileController::class, 'edit']
    )->name('profile.edit');

    Route::patch(
        '/profile',
        [ProfileController::class, 'update']
    )->name('profile.update');

    Route::delete(
        '/profile',
        [ProfileController::class, 'destroy']
    )->name('profile.destroy');

});

/* =========================
   ADMIN ROUTES
   Khusus admin biasa.
========================= */
Route::middleware([
    'auth',
    'admin'
])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get(
            '/dashboard',
            [AdminDashboardController::class, 'index']
        )->name('dashboard');

        Route::get(
            '/profile',
            [AdminProfileController::class, 'index']
        )->name('profile.index');

        Route::patch(
            '/profile',
            [AdminProfileController::class, 'update']
        )->name('profile.update');

        Route::get(
            '/laporan',
            [LaporanController::class, 'index']
        )->name('laporan.index');

        Route::get(
            '/laporan/update-status',
            [LaporanController::class, 'updateStatusIndex']
        )->name('laporan.update-status');

        Route::get(
            '/laporan/riwayat-status',
            [LaporanController::class, 'riwayatStatusIndex']
        )->name('laporan.riwayat-status');

        Route::get(
            '/laporan/{id}',
            [LaporanController::class, 'show']
        )->name('laporan.show');

        Route::put(
            '/laporan/{id}',
            [LaporanController::class, 'update']
        )->name('laporan.update');

        Route::get(
            '/statistik',
            [StatistikController::class, 'index']
        )->name('statistik.index');

        Route::resource(
            'manajemen-faq',
            TabelFaqController::class
        )->names([
            'index' => 'manajemen-faq.index',
            'create' => 'manajemen-faq.create',
            'store' => 'manajemen-faq.store',
            'show' => 'manajemen-faq.show',
            'edit' => 'manajemen-faq.edit',
            'update' => 'manajemen-faq.update',
            'destroy' => 'manajemen-faq.destroy',
        ])->parameters([
            // WAJIB. Tanpa baris ini, `Route::resource('manajemen-faq', ...)`
            // membuat URI `/admin/manajemen-faq/{manajemen_faq}/edit`.
            // `->names([...])` hanya menimpa NAMA route, bukan nama
            // parameter di URI.
            //
            // Akibatnya: implicit route model binding mencari parameter
            // bernama `manajemen_faq`, sementara controller-nya
            // `show(TabelFaq $faq)`. Tidak ada yang cocok, jadi
            // `ImplicitRouteBinding` melewatkannya dan container
            // mengembalikan instance `TabelFaq` KOSONG. Halaman show
            // lalu "berhasil" dirender dengan model tanpa id, dan
            // `route('admin.manajemen-faq.edit', $tabelFaq->id_faq)`
            // meledak:
            //
            //   Missing required parameter for [Route: admin.manajemen-faq.edit]
            //   [URI: admin/manajemen-faq/{manajemen_faq}/edit]
            //
            // `edit($id)`, `update(..., $id)`, dan `destroy($id)` punya
            // masalah serupa: `$id` tidak akan pernah terisi.
            //
            // Setelah ini URI-nya jadi `{faq}` dan cocok dengan
            // `TabelFaq $faq` di controller.
            'manajemen-faq' => 'faq',
        ]);

    });

/* =========================
   SUPER ADMIN ROUTES
   Terpisah total dari admin.
========================= */
Route::middleware([
    'auth',
    SuperAdminMiddleware::class
])
    ->prefix('superadmin')
    ->name('superadmin.')
    ->group(function () {

        Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])
            ->name('dashboard');

        Route::resource(
            'accounts',
            AccountController::class
        )
            ->parameters([
                'accounts' => 'account',
            ])
            ->except([
                'show',
            ]);

        // Aktifkan kembali akun yang sebelumnya dinonaktifkan.
        //
        // `withTrashed()` itu WAJIB di sini. Tanpa itu, route model
        // binding hanya mencari user yang belum di-soft-delete, jadi
        // `User $account` akan selalu lempar ModelNotFoundException
        // (404) tepat untuk akun yang justru sedang dipulihkan --
        // tombol "Aktifkan Kembali" di halaman daftar tidak akan pernah
        // bisa bekerja.
        Route::patch('/accounts/{account}/restore', [AccountController::class, 'restore'])
            ->withTrashed()
            ->name('accounts.restore');

    });

require __DIR__ . '/auth.php';

/*
    Halaman 404 dirender lewat route, bukan lewat exception handler.

    Tanpa ini, URL yang tidak cocok apa pun dilempar 404 oleh router
    SEBELUM middleware group `web` jalan. Group itu yang menjalankan
    `StartSession`, jadi `auth()` selalu null di halaman 404 -- dan
    link "Ke Dashboard" sesuai role di `errors/404.blade.php` diam-diam
    tidak pernah muncul untuk siapa pun, apa pun role-nya.

    Status HTTP tetap 404; yang berubah hanya view mana yang dipakai.
    Login wrong-role tetap dialihkan ke dashboard sendiri, bukan ke sini.
*/
Route::fallback(fn () => response()->view('errors.404', [], 404));


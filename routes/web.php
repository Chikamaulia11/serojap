<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\TabelFaqController;
use App\Http\Controllers\Pelapor\FaqController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\StatistikController;

/* =========================
   LANDING PAGE
========================= */
Route::get('/', function () {

    return view('welcome');

});

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
    );

    // =========================
    // REGISTER
    // =========================
    Route::get(
        '/register',
        [RegisteredUserController::class, 'create']
    )->name('register');

    Route::post(
        '/register',
        [RegisteredUserController::class, 'store']
    );

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

});

/* =========================
   LOGOUT
========================= */
Route::post(
    '/logout',
    [AuthenticatedSessionController::class, 'destroy']
)
    ->middleware('auth')
    ->name('logout');

/* =========================
   PROTECTED (PELAPOR)
========================= */
Route::middleware([
    'auth',
    'pelapor'
])->group(function () {

    // =========================
    // DASHBOARD
    // =========================
    Route::get(
        '/dashboard',
        [DashboardController::class, 'index']
    )->name('dashboard');

    // =========================
    // FORM LAPORAN
    // =========================
    Route::get(
        '/report',
        [ReportController::class, 'create']
    )->name('laporan.create');

    // =========================
    // SIMPAN LAPORAN
    // =========================
    Route::post(
        '/report',
        [ReportController::class, 'store']
    )->name('laporan.store');

    // =========================
    // MY REPORT / RIWAYAT
    // =========================
    Route::get(
        '/my-report',
        [ReportController::class, 'myReport']
    )->name('laporan.my-report');

    // =========================
    // FAQ
    // =========================
    Route::get(
        '/pusat-bantuan',
        [FaqController::class, 'index']
    )->name('pelapor.faq');

    // =========================
    // PROSEDUR
    // =========================
    Route::get('/prosedur', function () {

        return view('pelapor.prosedur');

    })->name('prosedur');

    // =========================
    // PROFILE
    // =========================
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
========================= */
Route::middleware([
    'auth',
    'admin'
])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // =========================
        // DASHBOARD ADMIN
        // =========================
        Route::get(
            '/dashboard',
            [AdminDashboardController::class, 'index']
        )->name('dashboard');

        // =========================
        // DAFTAR LAPORAN
        // =========================
        Route::get(
            '/laporan',
            [LaporanController::class, 'index']
        )->name('laporan.index');

        // =========================
        // HALAMAN UPDATE STATUS
        // =========================
        Route::get(
            '/laporan/update-status',
            [LaporanController::class, 'updateStatusIndex']
        )->name('laporan.update-status');

        // =========================
        // RIWAYAT STATUS
        // =========================
        Route::get(
            '/laporan/riwayat-status',
            [LaporanController::class, 'riwayatStatusIndex']
        )->name('laporan.riwayat-status');

        // =========================
        // DETAIL LAPORAN
        // =========================
        Route::get(
            '/laporan/{id}',
            [LaporanController::class, 'show']
        )->name('laporan.show');

        // =========================
        // UPDATE STATUS LAPORAN
        // =========================
        Route::put(
            '/laporan/{id}',
            [LaporanController::class, 'update']
        )->name('laporan.update');

        // =========================
        // STATISTIK
        // =========================
        Route::get(
            '/statistik',
            [StatistikController::class, 'index']
        )->name('statistik.index');

        // =========================
        // FAQ MANAGEMENT
        // =========================
        Route::resource(
            'manajemen-faq',
            TabelFaqController::class
        )->names([

            'index'   => 'manajemen-faq.index',
            'create'  => 'manajemen-faq.create',
            'store'   => 'manajemen-faq.store',
            'show'    => 'manajemen-faq.show',
            'edit'    => 'manajemen-faq.edit',
            'update'  => 'manajemen-faq.update',
            'destroy' => 'manajemen-faq.destroy',

        ]);

    });

require __DIR__ . '/auth.php';
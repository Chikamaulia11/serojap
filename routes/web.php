<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;

use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\TabelFaqController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\StatistikController;
use App\Http\Controllers\Admin\AdminProfileController;

use App\Http\Controllers\SuperAdmin\AccountController;

use App\Http\Controllers\Pelapor\FaqController;

use App\Http\Middleware\SuperAdminMiddleware;

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
    )->name('login.post');

    // =========================
    // REGISTER PELAPOR
    // Pelapor tetap boleh registrasi sendiri.
    // Admin dan super admin tidak registrasi sendiri.
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
    )->name('laporan.store');

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

        Route::get('/dashboard', function () {
            return view('superadmin.dashboard');
        })->name('dashboard');

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

    });

require __DIR__ . '/auth.php';
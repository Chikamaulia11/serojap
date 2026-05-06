<?php

use Illuminate\Support\Facades\Route;
<<<<<<< HEAD
<<<<<<< HEAD
use App\Http\Controllers\Auth\AuthenticatedSessionController;
<<<<<<< HEAD
use App\Http\Controllers\Admin\LaporanController;
=======
use App\Http\Controllers\ReportController;
=======
use App\Http\Controllers\AuthController;
>>>>>>> ed81696 (setup auth pelapor + middleware + dashboard final)
=======
>>>>>>> af1bf81 (setup final)
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;

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

    // LOGIN PELAPOR
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    // REGISTER
    Route::get('/register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('/register', [RegisteredUserController::class, 'store']);
});

/* =========================
   LOGOUT
========================= */
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

/* =========================
   PROTECTED (PELAPOR)
========================= */
Route::middleware(['auth', 'pelapor'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/report', [ReportController::class, 'create']);
    Route::post('/report', [ReportController::class, 'store']);

    Route::get('/my-report', [ReportController::class, 'index']);

    Route::get('/faq', function () {
        return view('pelapor.faq');
    });

    Route::get('/prosedur', function () {
        return view('pelapor.prosedur');
    });
<<<<<<< HEAD

<<<<<<< HEAD
    /* LOGOUT */
    Route::post('/logout', [AuthController::class, 'logout']);

<<<<<<< HEAD
<<<<<<< HEAD
Route::post('/report', [ReportController::class, 'store']);

Route::get('/my-report', [ReportController::class, 'myReport']);
>>>>>>> 43717ca (fix workspace clean)

// Web Routes

Route::get('/', function () {
    return view('welcome');
});

// LOGIN PELAPOR
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);

// LOGIN ADMIN
Route::get('/login/admin', [AuthenticatedSessionController::class, 'createAdmin'])->name('login.admin');
Route::post('/login/admin', [AuthenticatedSessionController::class, 'store'])->name('login.admin.post');

// DASHBOARD PELAPOR
Route::get('/dashboard', function () {
    return view('pelapor.dashboard');
})->middleware(['auth', 'verified', 'pelapor'])->name('dashboard');

// PROFILE
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ADMIN ROUTES (auth + admin middleware)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard Admin
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // --- LAPORAN ---
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/update-status', [LaporanController::class, 'updateStatusIndex'])->name('laporan.update-status');
    Route::get('/laporan/riwayat-status', [LaporanController::class, 'riwayatStatusIndex'])->name('laporan.riwayat-status');
    Route::get('/laporan/{id}', [LaporanController::class, 'show'])->name('laporan.show');
    Route::put('/laporan/{id}', [LaporanController::class, 'update'])->name('laporan.update');

});

require __DIR__.'/auth.php';
=======
Route::get('/my-report', function () {
    return view('pelapor.riwayat');
});
>>>>>>> fba5a8a (save progress dashboard pelapor)
=======
});
>>>>>>> ed81696 (setup auth pelapor + middleware + dashboard final)
=======
});
>>>>>>> af1bf81 (setup final)
=======
});
>>>>>>> b793b33 (backup sebelum merge)

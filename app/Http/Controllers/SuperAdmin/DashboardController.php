<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\TabelFaq;
use App\Models\User;
use App\Support\LaporanStats;

class DashboardController extends Controller
{
    public function index()
    {
        $ringkasanLaporan = LaporanStats::ringkasan();

        // Semua angka akun dihitung dengan `withTrashed()`.
        //
        // Tanpa itu, `User::count()` otomatis mengabaikan akun yang
        // sudah di-nonaktifkan: super admin akan melihat angka total
        // yang terus berkurang setiap kali dia menonaktifkan seseorang,
        // padahal datanya masih ada dan masih bisa dipulihkan. Angka
        // "nonaktif" juga harus menghitung akun soft-deleted -- kalau
        // tidak, angka itu tidak akan pernah bertambah.
        $semua = fn () => User::withTrashed();

        $akun = [
            'total' => $semua()->count(),
            'aktif' => User::where('is_active', true)->count(),
            'nonaktif' => $semua()->where(fn ($q) => $q->where('is_active', false)->orWhereNotNull('deleted_at'))->count(),
            'admin' => $semua()->where('role', User::ROLE['admin'])->count(),
            'pelapor' => $semua()->where('role', User::ROLE['pelapor'])->count(),
            'superAdmin' => $semua()->where('role', User::ROLE['super_admin'])->count(),
        ];

        // Admin yang belum pernah mengubah status satu pun laporan
        // yang masuk 30 hari terakhir, supaya super admin bisa melihat
        // petugas yang menganggur.
        $petugasAktif = User::query()
            ->where('role', User::ROLE['admin'])
            ->where('is_active', true)
            ->whereHas('statuses', fn ($q) => $q->where('created_at', '>=', now()->subDays(30)))
            ->count();

        $faqTerbaru = TabelFaq::with('admin')
            ->latest('id_faq')
            ->limit(5)
            ->get();

        $laporanTerbaru = Report::with(['user', 'latestStatus'])
            ->latest()
            ->limit(5)
            ->get();

        return view('superadmin.dashboard', compact(
            'ringkasanLaporan',
            'akun',
            'petugasAktif',
            'faqTerbaru',
            'laporanTerbaru'
        ));
    }
}

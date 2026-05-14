<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // =========================
        // TOTAL SEMUA LAPORAN
        // =========================
        $total = Report::count();

        // =========================
        // SUBQUERY: ambil id_status terbaru (MAX) per report_id
        // MAX(id_status) lebih aman dari MAX(created_at) karena id pasti unik
        // =========================
        $latestIds = DB::table('tabel_status')
            ->select(DB::raw('MAX(id_status) as id_status'))
            ->groupBy('report_id');

        $countByStatus = function (string $status) use ($latestIds): int {
            return DB::table('tabel_status')
                ->joinSub($latestIds, 'latest', 'tabel_status.id_status', '=', 'latest.id_status')
                ->where('tabel_status.status', $status)
                ->count();
        };

        $diterima = $countByStatus('diterima');
        $diproses = $countByStatus('diproses');
        $selesai  = $countByStatus('selesai');
        $ditolak  = $countByStatus('ditolak');

        return view('admin.dashboard', compact(
            'total', 'diterima', 'diproses', 'selesai', 'ditolak'
        ));
    }
}
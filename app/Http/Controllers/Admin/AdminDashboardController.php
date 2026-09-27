<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Support\LaporanStats;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = LaporanStats::ringkasan();

        // Laporan yang menunggu tindakan petugas lebih dari 3 hari.
        $perluTindakan = LaporanStats::perluTindakan(3);

        // Laporan terbaru supaya petugas bisa langsung bekerja dari
        // antrean, bukan harus mencari satu per satu lewat menu.
        $antrean = Report::with(['user', 'latestStatus'])
            ->latest()
            ->limit(8)
            ->get();

        return view('admin.dashboard', compact('stats', 'perluTindakan', 'antrean'));
    }
}

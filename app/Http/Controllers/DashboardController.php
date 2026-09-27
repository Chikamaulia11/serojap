<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\TabelFaq;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // =========================
        // STATISTIK LAPORAN SAYA
        //
        // Angka-angka ini sebelumnya sudah dihitung tapi tidak pernah
        // dipakai di view, jadi lima COUNT() terbuang sia-sia di
        // setiap load halaman. Sekarang dipakai di kartu ringkasan.
        // =========================
        $latestIds = DB::table('tabel_status')
            ->select(DB::raw('MAX(id_status) as id_status'))
            ->groupBy('report_id');

        $count = fn (string $status): int => DB::table('reports')
            ->joinSub($latestIds, 'latest', 'tabel_status.report_id', '=', 'reports.id')
            ->join('tabel_status', 'tabel_status.id_status', '=', 'latest.id_status')
            ->where('reports.user_id', $user->id)
            ->where('tabel_status.status', $status)
            ->count();

        $stats = [
            'total' => Report::where('user_id', $user->id)->count(),
            'diterima' => $count('diterima'),
            'diproses' => $count('diproses'),
            'selesai' => $count('selesai'),
            'ditolak' => $count('ditolak'),
        ];

        $stats['dikerjakan'] = $stats['diproses'] + $stats['selesai'];
        $stats['rasioSelesai'] = $stats['total'] > 0
            ? round(($stats['selesai'] / $stats['total']) * 100)
            : 0;

        // =========================
        // LAPORAN TERBARU
        // =========================
        $reports = Report::with(['latestStatus', 'statuses.admin'])
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        // =========================
        // FAQ
        // =========================
        $faqs = TabelFaq::orderBy('urutan')->limit(8)->get();

        return view('pelapor.dashboard', compact(
            'user',
            'stats',
            'reports',
            'faqs'
        ));
    }
}

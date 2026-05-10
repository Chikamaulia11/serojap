<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TabelLaporan;
use App\Models\TabelStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
    /**
     * MENAMPILKAN HALAMAN GRAFIK STATISTIK
     * Fungsi ini yang tadi hilang/belum ada di file kamu
     */
    public function statistik()
    {
        // 1. Data untuk Grafik Bulanan
        $dataLaporan = TabelLaporan::selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
            ->whereYear('created_at', date('Y'))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        $labels = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];
        $chartData = array_fill(0, 12, 0);

        foreach ($dataLaporan as $item) {
            $chartData[$item->bulan - 1] = $item->total;
        }

        // 2. Data untuk Kotak Ringkasan (Cards)
        $statusCounts = [
            'total'   => TabelLaporan::count(),
            'baru'    => TabelLaporan::doesntHave('statuses')->count(),
            'proses'  => TabelStatus::where('status', 'proses')->distinct('id_laporan')->count('id_laporan'),
            'selesai' => TabelStatus::where('status', 'selesai')->distinct('id_laporan')->count('id_laporan'),
            'ditolak' => TabelStatus::where('status', 'ditolak')->distinct('id_laporan')->count('id_laporan'),
        ];

        return view('admin.statistik', compact('labels', 'chartData', 'statusCounts'));
    }

    /**
     * MENAMPILKAN DAFTAR LAPORAN (INDEX)
     */
    public function index(Request $request)
    {
        $query = TabelLaporan::with(['user', 'statusTerbaru'])
            ->when($request->search, function ($q) use ($request) {
                $q->where('kecamatan', 'like', '%' . $request->search . '%')
                  ->orWhere('lokasi', 'like', '%' . $request->search . '%');
            })
            ->when($request->status, function ($q) use ($request) {
                if ($request->status === 'baru') {
                    $q->doesntHave('statuses');
                } else {
                    $q->whereHas('statuses', function ($sq) use ($request) {
                        $sq->where('status', $request->status);
                    });
                }
            })
            ->orderBy('created_at', 'desc');

        $laporan = $query->paginate(10);

        return view('admin.laporan.index', compact('laporan'));
    }

    /**
     * MENAMPILKAN DETAIL LAPORAN
     */
    public function show(string $id)
    {
        $laporan = TabelLaporan::with([
            'user',
            'statuses.admin',
            'statusTerbaru',
        ])->findOrFail($id);

        $daftarLaporan = TabelLaporan::select('id_laporan', 'kecamatan')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.laporan.show', [
            'laporan'       => $laporan,
            'daftarLaporan' => $daftarLaporan,
        ]);
    }

    /**
     * UPDATE STATUS LAPORAN
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'status'         => 'required|string|in:diterima,proses,selesai,ditolak',
            'keterangan'     => 'nullable|string|max:1000',
            'foto_perbaikan' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $laporan = TabelLaporan::findOrFail($id);

        $data = [
            'id_laporan'  => $laporan->id_laporan,
            'status'      => $request->status,
            'keterangan'  => $request->keterangan ?? null,
            'admin_id'    => auth()->id(),
        ];

        if ($request->hasFile('foto_perbaikan')) {
            $data['foto_perbaikan'] = $request->file('foto_perbaikan')->store('perbaikan', 'public');
        }

        TabelStatus::create($data);

        return redirect()
            ->route('admin.laporan.show', $id)
            ->with('success', 'Status laporan berhasil diperbarui!');
    }

    public function updateStatusIndex(Request $request)
    {
        $laporanSaatIni = TabelLaporan::query()->latest('created_at')->first();
        if (!$laporanSaatIni) {
            return redirect()->route('admin.laporan.index')->with('error', 'Belum ada laporan.');
        }
        return redirect()->route('admin.laporan.show', $laporanSaatIni->id_laporan);
    }

    public function riwayatStatusIndex(Request $request)
    {
        return redirect()->route('admin.laporan.index');
    }
}
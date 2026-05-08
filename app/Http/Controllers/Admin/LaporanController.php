<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TabelLaporan;
use App\Models\TabelStatus;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
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

    public function update(Request $request, string $id)
    {
        $request->validate([
            'status'         => 'required|string|in:diterima,proses,selesai,ditolak',
            'keterangan'     => 'nullable|string|max:1000',
            // ✅ Validasi foto
            'foto_perbaikan' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $laporan = TabelLaporan::findOrFail($id);

        $data = [
            'id_laporan'  => $laporan->id_laporan,
            'status'      => $request->status,
            'keterangan'  => $request->keterangan ?? null,
            // ✅ Simpan admin yang melakukan update
            'admin_id'    => auth()->id(),
        ];

        // ✅ Simpan foto jika ada
        if ($request->hasFile('foto_perbaikan')) {
            $data['foto_perbaikan'] = $request->file('foto_perbaikan')
                                              ->store('perbaikan', 'public');
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

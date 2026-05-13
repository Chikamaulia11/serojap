<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\TabelStatus;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    // =========================
    // DAFTAR LAPORAN
    // =========================
    public function index(Request $request)
    {
        $query = Report::with([
                'user',
                'latestStatus'
            ])

            // =========================
            // SEARCH
            // =========================
            ->when($request->search, function ($q) use ($request) {

                $q->where(function ($subQuery) use ($request) {

                    $subQuery->where(
                        'alamat',
                        'like',
                        '%' . $request->search . '%'
                    )

                    ->orWhere(
                        'keterangan',
                        'like',
                        '%' . $request->search . '%'
                    );

                });

            })

            // =========================
            // FILTER STATUS
            // =========================
            ->when($request->status, function ($q) use ($request) {

                $q->whereHas('latestStatus', function ($statusQuery) use ($request) {

                    $statusQuery->where(
                        'status',
                        $request->status
                    );

                });

            })

            // =========================
            // URUTAN TERBARU
            // =========================
            ->latest();

        $laporan = $query->paginate(10);

        return view(
            'admin.laporan.index',
            compact('laporan')
        );
    }

    // =========================
    // DETAIL LAPORAN
    // =========================
    public function show(string $id)
    {
        $laporan = Report::with([

                'user',
                'statuses.admin',
                'latestStatus'

            ])
            ->findOrFail($id);

        $daftarLaporan = Report::select(
                'id',
                'alamat'
            )
            ->latest()
            ->get();

        return view('admin.laporan.show', [

            'laporan'       => $laporan,
            'daftarLaporan' => $daftarLaporan,

        ]);
    }

    // =========================
    // UPDATE STATUS
    // =========================
    public function update(Request $request, string $id)
    {
        $request->validate([
            'status'     => 'required|in:diterima,diproses,selesai,ditolak',
            'keterangan' => 'required|string|min:5',
            'foto_perbaikan' => 'nullable|image|max:2048',
]);

        $laporan = Report::findOrFail($id);

        $fotoPerbaikan = null;

        // =========================
        // UPLOAD FOTO PERBAIKAN
        // =========================
        if ($request->hasFile('foto_perbaikan')) {

            $fotoPerbaikan = $request
                ->file('foto_perbaikan')
                ->store('perbaikan', 'public');

        }

        // =========================
        // SIMPAN RIWAYAT STATUS
        // =========================
        TabelStatus::create([

            'report_id'      => $laporan->id,
            'user_id'        => auth()->id(),
            'status'         => $request->status,
            'keterangan'     => $request->keterangan,
            'foto_perbaikan' => $fotoPerbaikan,

        ]);

        return redirect()

            ->route(
                'admin.laporan.show',
                $laporan->id
            )

            ->with(
                'success',
                'Status laporan berhasil diperbarui'
            );
    }

    // =========================
    // UPDATE STATUS PAGE
    // =========================
    public function updateStatusIndex(Request $request)
    {
        // Redirect langsung ke detail laporan terbaru
        $laporan = Report::with(['user', 'statuses.admin', 'latestStatus'])
            ->latest()
            ->first();

        if (!$laporan) {
            return redirect()
                ->route('admin.laporan.index')
                ->with('error', 'Belum ada laporan untuk diupdate.');
        }

        $daftarLaporan = Report::select('id', 'alamat')
            ->latest()
            ->get();

        // Pastikan property yang dipakai di view memang ada
        $daftarLaporan = $daftarLaporan->map(function ($item) {
            $item->id_laporan = $item->id;
            $item->kecamatan = $item->kecamatan ?? '—';
            return $item;
        });


        return view('admin.laporan.show', [
            'laporan' => $laporan,
            'daftarLaporan' => $daftarLaporan,
        ]);
    }


    // =========================
    // RIWAYAT STATUS
    // =========================
    public function riwayatStatusIndex(Request $request)
    {
        $riwayat = TabelStatus::with([

                'laporan',
                'admin'

            ])

            // =========================
            // FILTER SEARCH
            // =========================
            ->when($request->search, function ($q) use ($request) {

                $q->whereHas('laporan', function ($laporanQuery) use ($request) {

                    $laporanQuery->where(
                        'alamat',
                        'like',
                        '%' . $request->search . '%'
                    )

                    ->orWhere(
                        'keterangan',
                        'like',
                        '%' . $request->search . '%'
                    );

                });

            })

            // =========================
            // FILTER STATUS
            // =========================
            ->when($request->status, function ($q) use ($request) {

                $q->where(
                    'status',
                    $request->status
                );

            })

            // =========================
            // TERBARU
            // =========================
            ->latest('created_at')

            ->paginate(10);

        return view(

            'admin.laporan.riwayat-status',

            compact('riwayat')

        );
    }
}
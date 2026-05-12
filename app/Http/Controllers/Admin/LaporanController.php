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

            'status' => 'required|string|in:diterima,diproses,selesai,ditolak',

            'keterangan' =>
                'nullable|string|max:1000',

            'foto_perbaikan' =>
                'nullable|image|mimes:jpg,jpeg,png|max:2048',

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
        return $this->index($request);
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
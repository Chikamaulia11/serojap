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
     * Tampilkan daftar semua laporan masuk.
     * URL: GET /admin/laporan
     */
    public function index(Request $request)
    {
        $query = TabelLaporan::with(['user', 'statusTerbaru'])
                             ->orderBy('created_at', 'desc');

        // Filter berdasarkan status (opsional)
        if ($request->filled('status')) {
            $query->whereHas('statusTerbaru', function ($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        // Filter belum ada status (laporan baru masuk)
        if ($request->status === 'baru') {
            $query = TabelLaporan::with(['user', 'statusTerbaru'])
                                 ->doesntHave('statuses')
                                 ->orderBy('created_at', 'desc');
        }

        // Search berdasarkan kecamatan atau lokasi
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('kecamatan', 'like', '%' . $request->search . '%')
                  ->orWhere('lokasi', 'like', '%' . $request->search . '%');
            });
        }

        $laporan = $query->paginate(10)->withQueryString();

        return view('admin.laporan.index', compact('laporan'));
    }

    /**
     * Tampilkan detail satu laporan.
     * URL: GET /admin/laporan/{id}
     */
    public function show($id)
    {
        $laporan = TabelLaporan::with(['user', 'statuses.admin'])
                               ->findOrFail($id);

        return view('admin.laporan.show', compact('laporan'));
    }

    /**
     * Tampilkan halaman Update Status — daftar laporan dengan quick update.
     * URL: GET /admin/laporan/update-status
     */
    public function updateStatusIndex(Request $request)
    {
        $query = TabelLaporan::with(['user', 'statusTerbaru'])
                             ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->whereHas('statusTerbaru', function ($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        if ($request->status === 'baru') {
            $query = TabelLaporan::with(['user', 'statusTerbaru'])
                                 ->doesntHave('statuses')
                                 ->orderBy('created_at', 'desc');
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('kecamatan', 'like', '%' . $request->search . '%')
                  ->orWhere('lokasi', 'like', '%' . $request->search . '%');
            });
        }

        $laporan = $query->paginate(10)->withQueryString();

        return view('admin.laporan.update-status', compact('laporan'));
    }

    /**
     * Tampilkan halaman Riwayat Status — semua riwayat status.
     * URL: GET /admin/laporan/riwayat-status
     */
    public function riwayatStatusIndex(Request $request)
    {
        $query = TabelStatus::with(['laporan.user', 'admin'])
                            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->whereHas('laporan', function ($q) use ($request) {
                $q->where('kecamatan', 'like', '%' . $request->search . '%')
                  ->orWhere('lokasi', 'like', '%' . $request->search . '%');
            });
        }

        $riwayat = $query->paginate(15)->withQueryString();

        return view('admin.laporan.riwayat-status', compact('riwayat'));
    }

    /**
     * Proses verifikasi / update status laporan.
     * URL: PUT /admin/laporan/{id}
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status'      => 'required|in:diterima,ditolak,proses,selesai',
            'keterangan'  => 'nullable|string|max:500',
            'foto_perbaikan' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $laporan = TabelLaporan::findOrFail($id);

        // Upload foto perbaikan jika ada
        $fotoPath = null;
        if ($request->hasFile('foto_perbaikan')) {
            $fotoPath = $request->file('foto_perbaikan')
                                ->store('foto_perbaikan', 'public');
        }

        // Simpan status baru
        TabelStatus::create([
            'id_laporan'     => $laporan->id_laporan,
            'user_id'        => Auth::id(), // admin yang login
            'status'         => $request->status,
            'keterangan'     => $request->keterangan,
            'foto_perbaikan' => $fotoPath,
        ]);

        return redirect()
            ->route('admin.laporan.show', $id)
            ->with('success', 'Status laporan berhasil diperbarui.');
    }
}

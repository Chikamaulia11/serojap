<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TabelLaporan;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    /**
     * Display a listing of the resource.
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
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $laporan = TabelLaporan::with(['user', 'statuses'])->findOrFail($id);
        
        return view('admin.laporan.show', compact('laporan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required|string|in:diterima,proses,selesai,ditolak',
        ]);

        $laporan = TabelLaporan::findOrFail($id);

        // Create new status entry
        \App\Models\TabelStatus::create([
            'id_laporan' => $laporan->id_laporan,
            'status' => $request->status,
            'keterangan' => $request->keterangan ?? 'Status diperbarui oleh admin',
        ]);

        return redirect()->route('admin.laporan.index')->with('success', 'Status laporan berhasil diperbarui!');
    }
}

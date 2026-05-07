<?php

namespace App\Http\Controllers;

use App\Models\TabelLaporan;
use App\Models\TabelStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PelaporController extends Controller
{
    /**
     * Dashboard pelapor - lihat laporan sendiri
     */
    public function dashboard()
    {
        $laporans = TabelLaporan::with(['statusTerakhir'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        $stats = [
            'total' => TabelLaporan::where('user_id', Auth::id())->count(),
            'selesai' => TabelStatus::whereHas('laporan', fn($q) => $q->where('user_id', Auth::id()))
                ->where('status', 'selesai')->distinct('id_laporan')->count('id_laporan'),
        ];

        return view('pelapor.dashboard', compact('laporans', 'stats'));
    }

    // Add statusTerakhir relationship if missing
    public function getStatusTerakhirAttribute()
    {
        return $this->statuses()->latest()->first();
    }

    /**
     * Form submit laporan baru
     */
    public function create()
    {
        return view('pelapor.create-laporan');
    }

    /**
     * Simpan laporan baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'lokasi' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:100',
            'deskripsi' => 'required|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('laporan', 'public');
        }

        TabelLaporan::create([
            'user_id' => Auth::id(),
            'lokasi' => $request->lokasi,
            'kecamatan' => $request->kecamatan,
            'deskripsi' => $request->deskripsi,
            'foto' => $fotoPath,
        ]);

        return redirect()->route('pelapor.dashboard')
            ->with('success', 'Laporan berhasil dikirim dan menunggu verifikasi admin.');
    }

    /**
     * Detail laporan pelapor
     */
    public function show($id)
    {
        $laporan = TabelLaporan::with(['statuses.user', 'user'])
            ->where('id_laporan', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('pelapor.show-laporan', compact('laporan'));
    }
}

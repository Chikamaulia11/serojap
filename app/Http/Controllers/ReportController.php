<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    // =========================
    // FORM LAPORAN
    // =========================
    public function create()
    {
        return view('pelapor.form');
    }

    // =========================
    // SIMPAN LAPORAN
    // =========================
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'foto' => 'required|image',
            'alamat' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'keterangan' => 'required',
        ]);

        $foto = $request->file('foto')->store('reports', 'public');

        Report::create([
            'user_id' => Auth::id(), // 🔥 FIX DI SINI
            'nama_pelapor' => $request->nama,
            'foto' => $foto,
            'alamat' => $request->alamat,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'keterangan' => $request->keterangan,
            'status' => 'diterima'
        ]);

        return back()->with('success', 'Laporan berhasil dikirim');
    }

    // =========================
    // RIWAYAT LAPORAN USER
    // =========================
    public function index()
    {
        $reports = Report::where('user_id', Auth::id())
                    ->latest()
                    ->get();

        return view('pelapor.riwayat', compact('reports'));
    }
}
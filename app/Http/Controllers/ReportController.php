<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
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
            'user_id' => 1,
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

    public function myReport()
    {
        $reports = Report::latest()->get();
        return view('pelapor.riwayat', compact('reports'));
    }
}
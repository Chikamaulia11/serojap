<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\TabelStatus;
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
            'nama'        => 'required',
            'foto'        => 'required|image',
            'alamat'      => 'required',
            'latitude'    => 'required',
            'longitude'   => 'required',
            'keterangan'  => 'required',
        ]);

        // upload foto
        $foto = $request->file('foto')
            ->store('reports', 'public');

        // simpan laporan utama
        $report = Report::create([
            'user_id'       => Auth::id(),
            'nama_pelapor'  => $request->nama,
            'foto'          => $foto,
            'alamat'        => $request->alamat,
            'latitude'      => $request->latitude,
            'longitude'     => $request->longitude,
            'keterangan'    => $request->keterangan,
        ]);

        // status pertama otomatis
        TabelStatus::create([
            'report_id'   => $report->id,
            'user_id'     => Auth::id(),
            'status'      => 'diterima',
            'keterangan'  => 'Laporan berhasil dikirim',
        ]);

        return back()->with(
            'success',
            'Laporan berhasil dikirim'
        );
    }

    // =========================
    // RIWAYAT LAPORAN USER
    // =========================
    public function index()
    {
        $reports = Report::with('statusTerbaru')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view(
            'pelapor.riwayat',
            compact('reports')
        );
    }

    // =========================
    // MY REPORT
    // =========================
    public function myReport()
    {
        $reports = Report::with('statusTerbaru')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view(
            'pelapor.riwayat',
            compact('reports')
        );
    }
}
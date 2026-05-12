<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // =========================
        // TOTAL LAPORAN
        // =========================
        $total = Report::where(
            'user_id',
            $user->id
        )->count();

        // =========================
        // STATUS DITERIMA
        // =========================
        $diterima = Report::where(
                'user_id',
                $user->id
            )

            ->whereHas('latestStatus', function ($query) {

                $query->where(
                    'status',
                    'diterima'
                );

            })

            ->count();

        // =========================
        // STATUS DIPROSES
        // =========================
        $diproses = Report::where(
                'user_id',
                $user->id
            )

            ->whereHas('latestStatus', function ($query) {

                $query->where(
                    'status',
                    'diproses'
                );

            })

            ->count();

        // =========================
        // STATUS SELESAI
        // =========================
        $selesai = Report::where(
                'user_id',
                $user->id
            )

            ->whereHas('latestStatus', function ($query) {

                $query->where(
                    'status',
                    'selesai'
                );

            })

            ->count();

        // =========================
        // STATUS DITOLAK
        // =========================
        $ditolak = Report::where(
                'user_id',
                $user->id
            )

            ->whereHas('latestStatus', function ($query) {

                $query->where(
                    'status',
                    'ditolak'
                );

            })

            ->count();

        // =========================
        // DATA RIWAYAT DASHBOARD
        // =========================
        $reports = Report::with(
                'latestStatus'
            )

            ->where(
                'user_id',
                $user->id
            )

            ->latest()

            ->take(5)

            ->get();

        return view(
            'pelapor.dashboard',
            compact(

                'user',
                'total',
                'diterima',
                'diproses',
                'selesai',
                'ditolak',
                'reports'

            )
        );
    }
}
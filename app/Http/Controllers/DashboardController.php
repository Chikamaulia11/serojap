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

        $total = Report::where('user_id', $user->id)->count();
        $diterima = Report::where('user_id', $user->id)->where('status', 'diterima')->count();
        $diproses = Report::where('user_id', $user->id)->where('status', 'diproses')->count();
        $selesai = Report::where('user_id', $user->id)->where('status', 'selesai')->count();

        // ✅ FIX ERROR + DATA UNTUK SCROLL PAGE
        $reports = Report::where('user_id', $user->id)
                        ->latest()
                        ->take(5)
                        ->get();

        return view('pelapor.dashboard', compact(
            'user',
            'total',
            'diterima',
            'diproses',
            'selesai',
            'reports'
        ));
    }
}
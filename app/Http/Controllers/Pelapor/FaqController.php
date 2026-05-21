<?php

namespace App\Http\Controllers\Pelapor;

use App\Http\Controllers\Controller;
use App\Models\TabelFaq; 
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = TabelFaq::orderBy('urutan', 'asc')->get();
        return view('pelapor.faq', compact('faqs'));
    }
}

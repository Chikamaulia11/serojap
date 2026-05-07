<?php

namespace App\Http\Controllers;

use App\Models\TabelFaq;
use Illuminate\Http\Request;

class TabelFaqController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $faqs = TabelFaq::orderBy('urutan', 'asc')->get();
        return view('admin.faq', compact('faqs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pertanyaan' => 'required',
            'jawaban' => 'required',
        ]);

        TabelFaq::create([
            'user_id' => auth()->id(),
            'pertanyaan' => $request->pertanyaan,
            'jawaban' => $request->jawaban,
            'urutan' => $request->urutan ?? 0,
        ]);

        return redirect()->back()->with('success', 'Data FAQ berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(TabelFaq $tabelFaq)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TabelFaq $tabelFaq)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'pertanyaan' => 'required',
            'jawaban' => 'required',
        ]);

        $faq = TabelFaq::findOrFail($id);
        $faq->update([
            'pertanyaan' => $request->pertanyaan,
            'jawaban' => $request->jawaban,
            'urutan' => $request->urutan ?? 0,
        ]);

        return redirect()->back()->with('success', 'Data FAQ berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $faq = TabelFaq::findOrFail($id);
        $faq->delete();

        return redirect()->back()->with('success', 'FAQ berhasil dihapus!');
    }
}
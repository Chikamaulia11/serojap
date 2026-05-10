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
        return view('admin.faq.index', compact('faqs'));
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
        'pertanyaan' => 'required|string|max:255',
        'jawaban' => 'required|string',
        'urutan' => 'nullable|integer',
       ]);

        $urutan = $request->urutan ?? ( \App\Models\TabelFaq::max('urutan') + 1 );

        \App\Models\TabelFaq::create([
            'user_id'    => auth()->id(), 
            'pertanyaan' => $request->pertanyaan,
            'jawaban'    => $request->jawaban,
            'urutan'     => $urutan,
        ]);

        return redirect()->back()->with('success', 'Pertanyaan FAQ berhasil ditambahkan!');
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
    public function edit($id)
    {
        $faq = TabelFaq::findOrFail($id);
        return view('admin.faq.edit', compact('faq'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
       $request->validate([
            'pertanyaan' => 'required|string|max:255',
            'jawaban' => 'required|string',
            'urutan' => 'nullable|integer',
        ]);

        $faq = TabelFaq::findOrFail($id);
        
        $faq->update([
            'pertanyaan' => $request->pertanyaan,
            'jawaban' => $request->jawaban,
            'urutan' => $request->urutan ?? $faq->urutan,
        ]);

        return redirect()->route('admin.manajemen-faq.index')->with('success', 'Data FAQ berhasil diperbarui!');
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
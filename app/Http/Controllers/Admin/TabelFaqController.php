<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\TabelFaq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        $validator = Validator::make($request->all(), [
            'pertanyaan' => 'required|string|max:255',
            'jawaban'    => 'required|string',
            'urutan'     => 'nullable|integer|min:1|unique:tabel_faq,urutan', 
        ], [
        'urutan.unique' => 'Angka urutan tersebut sudah digunakan! Silakan gunakan nomor urut lain.',
       ]);

       if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $urutan = $request->urutan;
        if (is_null($urutan)) {
            $maxUrutan = TabelFaq::max('urutan') ?? 0; 
            $urutan = $maxUrutan + 1;
        }

        TabelFaq::create([
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
       $faq = TabelFaq::findOrFail($id);
       $validator = Validator::make($request->all(), [
            'pertanyaan' => 'required|string|max:255',
            'jawaban'    => 'required|string',
            'urutan'     => 'nullable|integer|min:1|unique:tabel_faq,urutan,' . $id . ',id_faq', 
        ], [
            'urutan.unique' => 'Angka urutan tersebut sudah digunakan! Silakan gunakan nomor urut lain.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', $validator->errors()->first('urutan'));
        }
        
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
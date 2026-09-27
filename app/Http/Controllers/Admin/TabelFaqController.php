<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TabelFaq;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TabelFaqController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $faqs = TabelFaq::with('admin')
            ->when($request->search, function ($query) use ($request) {
                $search = trim($request->search);

                $query->where(function ($sub) use ($search) {
                    $sub->where('pertanyaan', 'like', "%{$search}%")
                        ->orWhere('jawaban', 'like', "%{$search}%");
                });
            })
            ->orderBy('urutan')
            ->orderBy('id_faq')
            ->paginate(20)
            ->withQueryString();

        return view('admin.faq.index', compact('faqs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $faq = new TabelFaq([
            'urutan' => $this->urutanBerikutnya(),
        ]);

        return view('admin.faq.edit', [
            'faq' => $faq,
            'mode' => 'create',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pertanyaan' => ['required', 'string', 'max:255'],
            'jawaban' => ['required', 'string', 'max:5000'],
            'urutan' => ['nullable', 'integer', 'min:1', 'max:999'],
        ], [
            'urutan.unique' => 'Angka urutan tersebut sudah dipakai FAQ lain.',
        ], [
            'pertanyaan' => 'pertanyaan',
            'jawaban' => 'jawaban',
            'urutan' => 'urutan',
        ]);

        $this->pastikanUrutanBebas($validated['urutan'] ?? null);

        TabelFaq::create([
            'user_id' => auth()->id(),
            'pertanyaan' => trim($validated['pertanyaan']),
            'jawaban' => trim($validated['jawaban']),
            'urutan' => $validated['urutan'] ?? $this->urutanBerikutnya(),
        ]);

        return redirect()
            ->route('admin.manajemen-faq.index')
            ->with('success', 'Pertanyaan FAQ berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TabelFaq $faq)
    {
        $faq->load('admin');

        return view('admin.faq.show', ['tabelFaq' => $faq]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TabelFaq $faq)
    {
        return view('admin.faq.edit', [
            'faq' => $faq,
            'mode' => 'edit',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TabelFaq $faq)
    {
        $validated = $request->validate([
            'pertanyaan' => ['required', 'string', 'max:255'],
            'jawaban' => ['required', 'string', 'max:5000'],
            'urutan' => ['nullable', 'integer', 'min:1', 'max:999'],
        ], [
            'urutan.unique' => 'Angka urutan tersebut sudah dipakai FAQ lain.',
        ], [
            'pertanyaan' => 'pertanyaan',
            'jawaban' => 'jawaban',
            'urutan' => 'urutan',
        ]);

        $this->pastikanUrutanBebas($validated['urutan'] ?? null, $faq->id_faq);

        $faq->update([
            'pertanyaan' => trim($validated['pertanyaan']),
            'jawaban' => trim($validated['jawaban']),
            'urutan' => $validated['urutan'] ?? $faq->urutan,
        ]);

        return redirect()
            ->route('admin.manajemen-faq.index')
            ->with('success', 'Data FAQ berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TabelFaq $faq)
    {
        $faq->delete();

        return redirect()
            ->route('admin.manajemen-faq.index')
            ->with('success', 'FAQ berhasil dihapus.');
    }

    /**
     * Urutan berikutnya yang belum dipakai.
     */
    private function urutanBerikutnya(): int
    {
        return ((int) TabelFaq::max('urutan')) + 1;
    }

    /**
     * Validasi urutan di level aplikasi.
     *
     * Kolom `urutan` tidak punya unique index di database, jadi dua
     * request bersamaan bisa lolos pengecekan ini dan menghasilkan
     * urutan duplikat yang merusak urutan di halaman publik.
     */
    private function pastikanUrutanBebas(?int $urutan, ?int $kecualiId = null): void
    {
        if ($urutan === null) {
            return;
        }

        $dipakai = TabelFaq::where('urutan', $urutan)
            ->when($kecualiId, fn ($query) => $query->whereKeyNot($kecualiId))
            ->exists();

        if ($dipakai) {
            throw ValidationException::withMessages([
                'urutan' => 'Urutan ' . $urutan . ' sudah dipakai FAQ lain. Pilih nomor lain atau biarkan kosong untuk otomatis.',
            ]);
        }
    }
}

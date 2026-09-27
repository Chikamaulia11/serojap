@extends('layouts.admin')

@section('title', 'Detail FAQ — SEROJAP')

@section('content')
<div class="max-w-4xl mx-auto">

    {{-- Route `show` di resource `manajemen-faq` selalu terdaftar,
         jadi halaman ini wajib ada. Sebelumnya controller mengembalikan
         `admin.faq.show` yang tidak pernah dibuat file-nya: admin yang
         membuka /admin/manajemen-faq/{id} langsung dapat
         "View [admin.faq.show] not found". --}}

    <nav class="mb-4 text-sm" aria-label="Navigasi remah roti">
        <a href="{{ route('admin.manajemen-faq.index') }}" class="inline-block py-1 text-blue-700 hover:underline">
            &larr; Kembali ke daftar FAQ
        </a>
    </nav>

    <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">

        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-0">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-2.5 py-1 text-[11px] font-extrabold uppercase tracking-wide text-blue-700">
                    Urutan {{ $tabelFaq->urutan }}
                </span>

                <h1 class="mt-3 text-2xl font-bold leading-snug text-gray-900">
                    {{ $tabelFaq->pertanyaan }}
                </h1>
            </div>

            <div class="flex flex-shrink-0 gap-2">
                <a href="{{ route('admin.manajemen-faq.edit', $tabelFaq->id_faq) }}"
                   class="inline-flex items-center gap-1.5 rounded-xl border border-blue-200 bg-blue-50 px-3.5 py-2 text-xs font-bold text-blue-700 transition hover:bg-blue-100">
                    Edit
                </a>
            </div>
        </div>

        <div class="mt-6 border-t border-gray-100 pt-6">
            <h2 class="text-sm font-semibold text-gray-500">Jawaban</h2>

            {{-- `whitespace-pre-line` menjaga baris baru yang memang
                 diketik admin, seperti yang dilakukan di halaman FAQ
                 publik --}}
            <p class="mt-2 whitespace-pre-line text-[15px] leading-relaxed text-gray-800">
                {{ $tabelFaq->jawaban }}
            </p>
        </div>

        <dl class="mt-8 grid gap-4 border-t border-gray-100 pt-6 text-sm sm:grid-cols-3">
            <div>
                <dt class="text-xs font-bold uppercase tracking-wide text-gray-400">Ditambahkan oleh</dt>
                <dd class="mt-1 font-semibold text-gray-800">
                    {{ $tabelFaq->admin?->name ?? 'Akun admin tidak tersedia' }}
                </dd>
            </div>

            <div>
                <dt class="text-xs font-bold uppercase tracking-wide text-gray-400">Dibuat</dt>
                <dd class="mt-1 text-gray-800">
                    {{ $tabelFaq->created_at?->format('d F Y, H:i') ?? '-' }}
                </dd>
            </div>

            <div>
                <dt class="text-xs font-bold uppercase tracking-wide text-gray-400">Terakhir diubah</dt>
                <dd class="mt-1 text-gray-800">
                    {{ $tabelFaq->updated_at?->format('d F Y, H:i') ?? '-' }}
                </dd>
            </div>
        </dl>
    </article>

    {{-- Pratinjau konfirmasi tampilan di halaman publik: urutan FAQ
         menentukan urutan tampilnya di /pusat-bantuan. --}}
    <section class="mt-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="text-sm font-semibold text-gray-500">Tampil di halaman publik sebagai</h2>

        <details class="mt-3 rounded-xl border border-gray-200 bg-gray-50 p-4">
            <summary class="cursor-pointer text-base font-semibold text-gray-900">
                {{ $tabelFaq->pertanyaan }}
            </summary>

            <p class="mt-3 whitespace-pre-line text-sm leading-relaxed text-gray-700">
                {{ $tabelFaq->jawaban }}
            </p>
        </details>
    </section>

</div>
@endsection

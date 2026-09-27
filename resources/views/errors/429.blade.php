@php
    // Controller `ReportController@store` memanggil
    // `abort(429, 'Terlalu banyak laporan dikirim dari perangkat ini. Tunggu
    // N detik ...')`. Tanpa baris di bawah, pesan itu hilang: semua 429
    // tampil dengan kalimat yang sama walau penyebabnya berbeda.
    //
    // 429 dari middleware `throttle` bawaan Laravel pesannya
    // "Too Many Attempts." (bahasa Inggris), jadi yang bawaan itu
    // sengaja TIDAK ditampilkan -- supaya yang tampil tetap Bahasa
    // Indonesia.
    $pesanKustom = trim($exception?->getMessage() ?? '');

    if ($pesanKustom === '' || $pesanKustom === 'Too Many Attempts.') {
        $pesanKustom = null;
    }
@endphp

<x-error-layout>
    <div class="w-full max-w-lg text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-amber-100 text-amber-600 mb-6">
            <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z" />
            </svg>
        </div>

        <p class="text-6xl font-black text-slate-200">429</p>

        <h1 class="mt-3 text-2xl font-bold text-slate-900">Terlalu banyak permintaan</h1>

        <p class="mt-3 text-slate-600 leading-relaxed">
            @if ($pesanKustom)
                {{ $pesanKustom }}
            @else
                Kamu mengirim permintaan terlalu sering. Tunggu sekitar satu menit
                lalu coba lagi.
            @endif
        </p>

        <div class="mt-8">
            <a href="{{ url()->previous() }}"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-900 focus-visible:ring-offset-2">
                Halaman Sebelumnya
            </a>
        </div>
    </div>
</x-error-layout>

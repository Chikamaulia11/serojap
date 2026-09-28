<x-error-layout>
    <div class="w-full max-w-lg text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-rose-100 text-[var(--danger)] mb-6">
            <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M12 9v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        <p class="text-6xl font-black text-slate-200">500</p>

        <h1 class="mt-3 text-2xl font-bold text-slate-900">Sistem sedang bermasalah</h1>

        <p class="mt-3 text-slate-600 leading-relaxed">
            Ada gangguan di sisi kami. Data kamu tidak hilang. Coba muat ulang
            halaman dalam beberapa menit lagi.
        </p>

        <div class="mt-8">
            <a href="{{ url('/') }}"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-900 focus-visible:ring-offset-2">
                Kembali ke Beranda
            </a>
        </div>

        @if (config('app.debug'))
            <p class="mt-6 text-xs text-slate-400">
                Mode debug aktif. Rincian teknis ada di log server.
            </p>
        @endif
    </div>
</x-error-layout>

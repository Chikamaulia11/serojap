<x-error-layout>
    <div class="w-full max-w-lg text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-amber-100 text-amber-600 mb-6">
            <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M12 9v2m0 4h.01M5.07 19h13.86a2 2 0 001.74-3L13.74 4a2 2 0 00-3.48 0l-7 12a2 2 0 001.74 3z" />
            </svg>
        </div>

        <p class="text-6xl font-black text-slate-200">419</p>

        <h1 class="mt-3 text-2xl font-bold text-slate-900">Sesi kamu sudah berakhir</h1>

        <p class="mt-3 text-slate-600 leading-relaxed">
            Demi keamanan, sesi login diakhiri setelah terlalu lama tidak
            dipakai. Silakan login ulang lalu kirim ulang isianmu.
        </p>

        <div class="mt-8">
            <a href="{{ route('login') }}"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-900 focus-visible:ring-offset-2">
                Login Ulang
            </a>
        </div>
    </div>
</x-error-layout>

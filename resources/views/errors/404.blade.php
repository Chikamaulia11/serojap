<x-error-layout>
    <div class="w-full max-w-lg text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 mb-6">
            <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
            </svg>
        </div>

        <p class="text-6xl font-black text-slate-200">404</p>

        <h1 class="mt-3 text-2xl font-bold text-slate-900">Halaman tidak ditemukan</h1>

        <p class="mt-3 text-slate-600 leading-relaxed">
            Halaman yang kamu cari sudah dipindahkan atau tidak pernah ada.
            Periksa kembali tautannya, atau kembali ke beranda.
        </p>

        <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('beranda') }}"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-900 focus-visible:ring-offset-2">
                Kembali ke Beranda
            </a>

            {{-- `route('dashboard')` selalu menunjuk ke dashboard pelapor, jadi
                 admin/petugas yang membuka 404 lalu menekan ini akan mendarat di
                 halaman orang lain. `User::dashboardRoute()` mengikuti role. --}}
            @auth
                @if (auth()->user()->dashboardRoute())
                    <a href="{{ route(auth()->user()->dashboardRoute()) }}"
                            class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2">
                            Ke Dashboard
                    </a>
                @endif
            @endauth
        </div>
    </div>
</x-error-layout>

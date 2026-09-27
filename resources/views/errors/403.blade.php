<x-error-layout>
    <div class="w-full max-w-lg text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-rose-100 text-rose-600 mb-6">
            <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
            </svg>
        </div>

        <p class="text-6xl font-black text-slate-200">403</p>

        <h1 class="mt-3 text-2xl font-bold text-slate-900">Akses ditolak</h1>

        <p class="mt-3 text-slate-600 leading-relaxed">
            Kamu tidak punya izin untuk membuka halaman ini. Kalau ini tidak
            disengaja, mungkin kamu masuk dengan akun yang salah.
        </p>

        <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ url('/') }}"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-900 focus-visible:ring-offset-2">
                Kembali ke Beranda
            </a>

            @guest
                <a href="{{ route('login') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2">
                        Ganti Akun
                </a>
            @else
                {{-- Sudah masuk sebagai admin/petugas/super admin.

                     Tombol ini harus POST ke route logout, bukan `<a>` ke
                     halaman login. Semua route login dipagari middleware
                     `guest`, jadi user yang masih login akan dilempar
                     balik ke dashboard-nya sendiri -- persis ke halaman
                     yang baru saja menolak dia. Link-nya jadi hidup
                     tapi tidak melakukan apa pun.

                     Logout dulu, baru tampilkan halaman login area mereka.
                     `redirect` hanya diterima logout kalau persis salah
                     satu dari ketiga halaman login aplikasi ini; lihat
                     `AuthenticatedSessionController::tujuanSetelahLogout`.
                --}}
                @if (auth()->user()->loginRoute())
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <input type="hidden" name="redirect" value="{{ route(auth()->user()->loginRoute()) }}">

                        <button type="submit"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2">
                            Ganti Akun
                        </button>
                    </form>
                @endif
            @endguest
        </div>
    </div>
</x-error-layout>

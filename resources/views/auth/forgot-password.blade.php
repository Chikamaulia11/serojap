{{--
    View ini sebelumnya tidak pernah ada, padahal controller-nya
    (`PasswordResetLinkController`) sudah lengkap dan route-nya
    terdaftar. Setiap tamu yang menekan "Lupa kata sandi" di halaman
    login mendapat:

        View [auth.forgot-password] not found.

    Form sengaja dibuat identik pola Breeze supaya konsisten dengan
    halaman login, dan tetap memakai `x-guest-layout`.
--}}
<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Lupa kata sandi? Tidak masalah. Masukkan alamat email yang kamu pakai untuk masuk, lalu kami kirim tautan untuk mengatur ulang kata sandi.') }}
    </div>

    {{-- Status sukses
         Pesan yang sama ditampilkan baik untuk email terdaftar maupun
         tidak, supaya halaman ini tidak bisa dipakai menebak email
         mana yang punya akun. --}}
    @if (session('status'))
        <div
            class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800"
            role="status"
        >
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />

            <x-text-input
                id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="email"
                placeholder="nama@email.com"
            />

            <x-input-error
                :messages="$errors->get('email')"
                class="mt-2"
            />
        </div>

        <div class="flex flex-col items-center justify-between gap-3 sm:flex-row">
            <a
                href="{{ route('login') }}"
                class="inline-block py-1 text-sm text-gray-600 underline underline-offset-4 hover:text-gray-900"
            >
                {{ __('Kembali ke halaman masuk') }}
            </a>

            <x-primary-button>
                {{ __('Kirim Tautan Reset') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

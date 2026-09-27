{{--
    View ini sebelumnya tidak pernah ada, padahal route
    `password.reset` sudah terdaftar dan `PasswordResetController`
    sudah lengkap. Tautan reset yang dikirim ke email berakhir di
    "View [auth.reset-password] not found".
    --}}
<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Atur ulang kata sandi kamu. Setelah berhasil, kamu akan diarahkan ke halaman masuk untuk login dengan kata sandi baru.') }}
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf

        {{-- Token harus ikut terkirim; disimpan sebagai input
             tersembunyi karena nilainya sudah ada di URL. --}}
        <input type="hidden" name="token" value="{{ $token }}">

        <div>
            <x-input-label for="email" :value="__('Email')" />

            <x-text-input
                id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                value="{{ old('email', $email) }}"
                required
                autofocus
                autocomplete="email"
            />

            <x-input-error
                :messages="$errors->get('email')"
                class="mt-2"
            />
        </div>

        <div>
            <x-input-label for="password" :value="__('Kata Sandi Baru')" />

            <x-text-input
                id="password"
                class="block mt-1 w-full"
                type="password"
                name="password"
                required
                autocomplete="new-password"
            />

            {{-- Controller memvalidasi dengan `confirmed` dan
                 `Rules\Password::defaults()`, jadi batas panjang
                 minimum di sini harus sama supaya pengguna tidak
                 baru tahu syaratnya setelah menekan tombol. --}}
            <p class="mt-1.5 text-xs text-gray-500">
                {{ __('Minimal 8 karakter.') }}
            </p>

            <x-input-error
                :messages="$errors->get('password')"
                class="mt-2"
            />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Ulangi Kata Sandi Baru')" />

            <x-text-input
                id="password_confirmation"
                class="block mt-1 w-full"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
            />

            <x-input-error
                :messages="$errors->get('password_confirmation')"
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
                {{ __('Atur Ulang Kata Sandi') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-gray-800">
            Super Admin Login
        </h2>
        <p class="text-sm text-gray-500">
            Akses utama pengelolaan sistem SEROJAP
        </p>
    </div>

    <form method="POST" action="{{ route('login.superadmin.post') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />

            <x-text-input
                id="email"
                class="block mt-1 w-full border-gray-300 focus:border-[#2657c1] focus:ring-[#2657c1]"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
            />

            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input
                id="password"
                class="block mt-1 w-full border-gray-300 focus:border-[#2657c1] focus:ring-[#2657c1]"
                type="password"
                name="password"
                required
                autocomplete="current-password"
            />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input
                    id="remember_me"
                    type="checkbox"
                    class="rounded border-gray-300 text-[#2657c1] shadow-sm focus:ring-[#2657c1]"
                    name="remember"
                >

                <span class="ms-2 text-sm text-gray-600">
                    Remember me
                </span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-6">
            <x-primary-button class="w-full justify-center bg-[#2657c1] hover:bg-[#1f4674] py-3">
                LOGIN SUPER ADMIN
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
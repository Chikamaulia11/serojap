<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-gray-800">Admin Login</h2>
        <p class="text-sm text-gray-500">Administrator Serojap Purwakarta</p>
    </div>

    <form method="POST" action="{{ route('login.admin.post') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full border-gray-300 focus:border-[#226d71] focus:ring-[#226d71]" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full border-gray-300 focus:border-[#226d71] focus:ring-[#226d71]" type="password" name="password" required />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-[#226d71] shadow-sm focus:ring-[#226d71]" name="remember">
                <span class="ms-2 text-sm text-gray-600">Remember me</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-6">
            <x-primary-button class="w-full justify-center bg-[#226d71] hover:bg-[#1a5255] py-3">
                LOG IN 
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
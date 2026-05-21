<x-guest-layout>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>

            <x-input-label for="name" :value="__('Name')" />

            <x-text-input
                id="name"
                class="block mt-1 w-full"
                type="text"
                name="name"
                :value="old('name')"
                required
                autofocus
                autocomplete="name"
            />

            <x-input-error :messages="$errors->get('name')" class="mt-2" />

        </div>

        <!-- Email -->
        <div class="mt-4">

            <x-input-label for="email" :value="__('Email')" />

            <x-text-input
                id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
                autocomplete="username"
            />

            <p id="email-error"
               class="text-red-500 text-sm mt-1"
               style="display: none;">
                Harus menggunakan email @gmail.com
            </p>

            <x-input-error :messages="$errors->get('email')" class="mt-2" />

        </div>

        <!-- Password -->
        <div class="mt-4">

            <x-input-label for="password" :value="__('Password')" />

            <x-text-input
                id="password"
                class="block mt-1 w-full"
                type="password"
                name="password"
                required
                minlength="8"
                autocomplete="new-password"
            />

            <p id="password-error"
               class="text-red-500 text-sm mt-1"
               style="display: none;">
                Password minimal 8 karakter
            </p>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />

        </div>

        <!-- Confirm Password -->
        <div class="mt-4">

            <x-input-label
                for="password_confirmation"
                :value="__('Confirm Password')"
            />

            <x-text-input
                id="password_confirmation"
                class="block mt-1 w-full"
                type="password"
                name="password_confirmation"
                required
                minlength="8"
                autocomplete="new-password"
            />

            <p id="confirm-error"
               class="text-red-500 text-sm mt-1"
               style="display: none;">
                Password minimal 8 karakter
            </p>

            <x-input-error
                :messages="$errors->get('password_confirmation')"
                class="mt-2"
            />

        </div>

        <!-- BUTTON -->
        <div class="flex items-center justify-end mt-4">

            <a
                class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                href="{{ route('login') }}"
            >
                {{ __('Sudah Punya Akun?') }}
            </a>

            <x-primary-button
                class="ms-4 bg-[#227474] hover:bg-[#1b5e5e] text-white"
            >
                {{ __('Register') }}
            </x-primary-button>

        </div>

    </form>

    <style>
        .border-error {
            border-color: red !important;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.2) !important;
        }

        .border-normal {
            border-color: rgb(99, 102, 241) !important;
            box-shadow: none !important;
        }
    </style>

    <script>

        const emailInput = document.getElementById('email');
        const passInput = document.getElementById('password');
        const confirmInput = document.getElementById('password_confirmation');

        const emailError = document.getElementById('email-error');
        const passError = document.getElementById('password-error');
        const confirmError = document.getElementById('confirm-error');

        // VALIDASI EMAIL
        emailInput.addEventListener('input', function () {

            const value = this.value;

            if (!value.endsWith('@gmail.com')) {

                this.classList.add('border-error');
                this.classList.remove('border-normal');

                emailError.style.display = 'block';

            } else {

                this.classList.add('border-normal');
                this.classList.remove('border-error');

                emailError.style.display = 'none';
            }
        });

        emailInput.addEventListener('blur', function () {

            const value = this.value;

            if (value.length > 0 && !value.endsWith('@gmail.com')) {

                this.classList.add('border-error');
                this.classList.remove('border-normal');

                emailError.style.display = 'block';
            }
        });

        // VALIDASI PASSWORD
        function validatePass(input, errorElement) {

            if (input.value.length > 0 && input.value.length < 8) {

                input.classList.add('border-error');
                input.classList.remove('border-normal');

                errorElement.style.display = 'block';

            } else {

                input.classList.add('border-normal');
                input.classList.remove('border-error');

                errorElement.style.display = 'none';
            }
        }

        passInput.addEventListener('input', () => {
            validatePass(passInput, passError);
        });

        confirmInput.addEventListener('input', () => {
            validatePass(confirmInput, confirmError);
        });

    </script>

</x-guest-layout>
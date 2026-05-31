<section>

    <div class="card-title">
        <h2>Update Password</h2>

        <p>
            Jaga keamanan akun dengan password yang kuat.
        </p>
    </div>

    <div class="password-box">

        <p class="password-note">
            Gunakan password baru yang mudah kamu ingat, tapi sulit ditebak.
            Kosongkan bagian ini jika tidak ingin mengganti password.
        </p>

        <form
            method="POST"
            action="{{ route('password.update') }}"
        >

            @csrf
            @method('PUT')

            <!-- PASSWORD SAAT INI -->
            <div class="form-group">

                <label>Password Saat Ini</label>

                <input
                    type="password"
                    name="current_password"
                    class="form-input"
                    autocomplete="current-password"
                    placeholder="Masukkan password saat ini"
                >

                @error('current_password', 'updatePassword')
                    <div class="form-error">
                        {{ $message }}
                    </div>
                @enderror

            </div>

            <!-- PASSWORD BARU -->
            <div class="form-group">

                <label>Password Baru</label>

                <input
                    type="password"
                    name="password"
                    class="form-input"
                    autocomplete="new-password"
                    placeholder="Masukkan password baru"
                >

                @error('password', 'updatePassword')
                    <div class="form-error">
                        {{ $message }}
                    </div>
                @enderror

            </div>

            <!-- KONFIRMASI PASSWORD -->
            <div class="form-group">

                <label>Konfirmasi Password Baru</label>

                <input
                    type="password"
                    name="password_confirmation"
                    class="form-input"
                    autocomplete="new-password"
                    placeholder="Ulangi password baru"
                >

                @error('password_confirmation', 'updatePassword')
                    <div class="form-error">
                        {{ $message }}
                    </div>
                @enderror

            </div>

            <button
                type="submit"
                class="save-btn"
            >
                Simpan Password
            </button>

        </form>

    </div>

</section>
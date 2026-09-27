<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    public function edit(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $request->route('token'),
            'email' => $request->string('email')->toString(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'password.confirmed' => 'Konfirmasi kata sandi tidak sama dengan kata sandi baru.',
        ], [
            'token' => 'token',
            'email' => 'email',
            'password' => 'kata sandi baru',
        ]);

        $email = mb_strtolower(trim($validated['email']));

        // Akun nonaktif tidak boleh dihidupkan lewat tautan reset.
        $user = User::where('email', $email)->first();

        if ($user && ! $user->is_active) {
            throw ValidationException::withMessages([
                'email' => 'Akun ini sudah dinonaktifkan. Hubungi super admin untuk mengaktifkan kembali.',
            ]);
        }

        $status = Password::reset(
            [
                'email' => $email,
                'password' => $validated['password'],
                'password_confirmation' => $validated['password_confirmation'] ?? null,
                'token' => $validated['token'],
            ],
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => trans($status),
            ]);
        }

        return redirect()
            ->route('login')
            ->with('success', 'Kata sandi berhasil diubah. Silakan login dengan kata sandi baru.');
    }
}

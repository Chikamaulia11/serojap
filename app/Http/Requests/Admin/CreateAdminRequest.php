<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Akses endpoint dijaga oleh SuperAdminMiddleware.
        return true;
    }

    public function rules(): array
    {
        return [
            'role' => [
                'required',
                'string',
                'in:admin,pelapor',
            ],

            'nama' => [
                'required',
                'string',
                'min:3',
                'max:255',
            ],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'ends_with:@gmail.com',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],

            'password_confirmation' => [
                'required',
                'string',
                'min:8',
            ],

            'posisi' => [
                'nullable',
                'required_if:role,admin',
                'string',
                'min:2',
                'max:255',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'role.required' => 'Jenis akun wajib dipilih.',
            'role.in' => 'Jenis akun harus admin atau pelapor.',

            'nama.required' => 'Nama wajib diisi.',
            'nama.min' => 'Nama minimal 3 karakter.',

            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.ends_with' => 'Email harus menggunakan @gmail.com.',
            'email.unique' => 'Email ini sudah digunakan.',

            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',

            'password_confirmation.required' => 'Konfirmasi password wajib diisi.',
            'password_confirmation.min' => 'Konfirmasi password minimal 8 karakter.',

            'posisi.required_if' => 'Posisi wajib diisi untuk akun admin.',
            'posisi.min' => 'Posisi minimal 2 karakter.',
        ];
    }
}
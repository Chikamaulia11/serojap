<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Akses endpoint dijaga oleh middleware SuperAdminMiddleware.
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'min:3', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'ends_with:@gmail.com',
                'unique:users,email',
            ],
            'password' => ['required', 'string', 'size:8', 'confirmed'],
            'posisi' => ['required', 'string', 'min:2', 'max:255'],
        ];
    }

}


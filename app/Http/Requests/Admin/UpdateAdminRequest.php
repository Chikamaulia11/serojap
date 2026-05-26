<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UpdateAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Endpoint dijaga oleh middleware SuperAdminMiddleware.
        return true;
    }

    public function rules(): array
    {
        $adminId = $this->route('admin');

        $type = $this->input('type');

        // type:
        // - profile: update nama/email/posisi
        // - password: update password saja
        return match ($type) {
            'password' => [
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'password_confirmation' => ['required', 'string', 'min:8'],
            ],
            default => [
                'nama' => ['required', 'string', 'min:3', 'max:255'],
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    'ends_with:@gmail.com',
                    Rule::unique('users', 'email')->ignore($adminId),
                ],
                'posisi' => ['required', 'string', 'min:2', 'max:255'],
            ],
        };
    }
}


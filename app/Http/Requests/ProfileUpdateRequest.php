<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Pastikan ini di-set true agar request diizinkan masuk
        return true; 
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255'
            ],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],

            // KITA SESUAIKAN: Menggunakan 'profile_image' sesuai dengan atribut name di HTML view blade Anda
            'profile_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,gif',
                'max:2048' // Maksimal ukuran gambar 2MB
            ],
        ];
    }
}
<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(['admin', 'mitra'])],

            // Optional: if omitted, backend will generate a random password.
            'password' => ['nullable', 'string', 'min:8', 'max:255'],

            // Required for admin profile creation
            'username' => [
                Rule::requiredIf(fn () => $this->input('role') === 'admin'),
                'nullable',
                'string',
                'max:255',
            ],
            'instansi' => [
                Rule::requiredIf(fn () => $this->input('role') === 'admin'),
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }
}

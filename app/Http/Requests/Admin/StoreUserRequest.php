<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $password = $this->input('password');
        if ($password === '' || $password === null) {
            $this->merge(['password' => null]);
        }
    }

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

            // admins: nama + divisi (request fields: username, instansi)
            'username' => [
                Rule::excludeIf(fn () => $this->input('role') !== 'admin'),
                Rule::requiredIf(fn () => $this->input('role') === 'admin'),
                'nullable',
                'string',
                'max:255',
            ],
            'instansi' => [
                Rule::excludeIf(fn () => $this->input('role') !== 'admin'),
                Rule::requiredIf(fn () => $this->input('role') === 'admin'),
                'nullable',
                'string',
                'max:255',
            ],

            // mitras: nama_perusahaan, pic, no_handphone, alamat
            'nama_perusahaan' => [
                Rule::excludeIf(fn () => $this->input('role') !== 'mitra'),
                Rule::requiredIf(fn () => $this->input('role') === 'mitra'),
                'nullable',
                'string',
                'max:255',
            ],
            'pic' => [
                Rule::excludeIf(fn () => $this->input('role') !== 'mitra'),
                Rule::requiredIf(fn () => $this->input('role') === 'mitra'),
                'nullable',
                'string',
                'max:255',
            ],
            'no_handphone' => [
                Rule::excludeIf(fn () => $this->input('role') !== 'mitra'),
                Rule::requiredIf(fn () => $this->input('role') === 'mitra'),
                'nullable',
                'string',
                'max:32',
            ],
            'alamat' => [
                Rule::excludeIf(fn () => $this->input('role') !== 'mitra'),
                Rule::requiredIf(fn () => $this->input('role') === 'mitra'),
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }
}

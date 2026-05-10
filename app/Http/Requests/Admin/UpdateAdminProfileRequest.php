<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        $userId = (int) ($this->user()?->id_user ?? 0);

        return [
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId, 'id_user'),
            ],
            'nama' => ['required', 'string', 'max:255'],
            'divisi' => ['nullable', 'string', 'max:255'],
        ];
    }
}

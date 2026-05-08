<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        $userId = (int) $this->route('id');
        $targetRole = User::query()->whereKey($userId)->value('role');

        return [
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId, 'id_user'),
            ],

            // Optional: if provided, resets user's password.
            'password' => ['nullable', 'string', 'min:8', 'max:255'],

            // Required when updating admin profile fields
            'username' => [
                Rule::requiredIf(fn () => $targetRole === 'admin'),
                'nullable',
                'string',
                'max:255',
            ],
            'instansi' => [
                Rule::requiredIf(fn () => $targetRole === 'admin'),
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }
}

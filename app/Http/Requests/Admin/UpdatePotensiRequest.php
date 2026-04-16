<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePotensiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'judul' => ['sometimes', 'required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'status_tampil' => ['nullable', 'boolean'],
        ];
    }
}

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
            'kategori' => ['sometimes', 'required', 'string', 'max:50'],
            'judul' => ['sometimes', 'required', 'string', 'max:255'],
            'deskripsi' => ['sometimes', 'required', 'string', 'max:5000'],
            'gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'status_tampil' => ['nullable', 'boolean'],

            'poin' => ['nullable', 'array', 'max:50'],
            'poin.*' => ['nullable', 'string', 'max:255'],
        ];
    }
}

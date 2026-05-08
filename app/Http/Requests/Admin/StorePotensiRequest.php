<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StorePotensiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'kategori' => ['required', 'string', 'max:50'],
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string', 'max:5000'],
            'gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'status_tampil' => ['nullable', 'boolean'],

            // List of bullet points/"poin" shown under the category.
            'poin' => ['nullable', 'array', 'max:50'],
            'poin.*' => ['nullable', 'string', 'max:255'],
        ];
    }
}

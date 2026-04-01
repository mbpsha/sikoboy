<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePersetujuanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'status_persetujuan'  => ['required', Rule::in(['disetujui', 'revisi', 'ditolak'])],
            'catatan_persetujuan' => [
                Rule::requiredIf(fn() => in_array($this->status_persetujuan, ['revisi', 'ditolak'])),
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'catatan_persetujuan.required' => 'Catatan wajib diisi untuk status revisi atau ditolak.',
        ];
    }
}

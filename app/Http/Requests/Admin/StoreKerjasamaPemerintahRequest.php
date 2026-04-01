<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreKerjasamaPemerintahRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'judul'            => ['required', 'string', 'max:255'],
            'nomor_surat'      => ['required', 'string', 'max:100'],
            'urusan'           => ['required', 'string', 'max:255'],
            'daerah'           => ['required', 'string', 'max:255'],
            'jenis_kerjasama'  => ['required', 'string', 'max:100'],
            'jenis_dokumen'    => ['required', 'string', 'max:100'],
            'nama_pihak_luar'  => ['required', 'string', 'max:255'],
            'tanggal_mulai'    => ['required', 'date'],
            'tanggal_berakhir' => ['required', 'date', 'after:tanggal_mulai'],
            'keterangan'       => ['nullable', 'string'],
            'id_kategori'      => ['nullable', 'integer', 'exists:kategori_kerjasama,id_kategori'],
        ];
    }

    public function messages(): array
    {
        return [
            'tanggal_berakhir.after' => 'Tanggal berakhir harus setelah tanggal mulai.',
        ];
    }
}

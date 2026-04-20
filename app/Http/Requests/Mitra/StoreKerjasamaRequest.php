<?php

namespace App\Http\Requests\Mitra;

use Illuminate\Foundation\Http\FormRequest;

class StoreKerjasamaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'mitra';
    }

    public function rules(): array
    {
        return [
            'jenis_kerjasama' => ['required', 'string', 'max:100'],
            'jenis_dokumen' => ['required', 'string', 'max:100'],
            'judul' => ['required', 'string', 'max:255'],
            'nama_pihak_luar' => ['required', 'string', 'max:255'],
            'nomor_suratM' => ['required', 'string', 'max:100'],
            'pembiayaan' => ['required', 'string', 'max:255'],
            'urusan' => ['required', 'string', 'max:255'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after:tanggal_mulai'],
            'dokumen_file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ];
    }
}

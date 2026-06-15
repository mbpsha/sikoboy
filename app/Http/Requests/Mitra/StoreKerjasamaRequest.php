<?php

namespace App\Http\Requests\Mitra;

use App\Enums\UrusanEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreKerjasamaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'mitra';
    }

    protected function prepareForValidation()
    {
        if ($this->has('urusan')) {
            $this->merge([
                'urusan' => mb_strtoupper($this->urusan),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'jenis_kerjasama' => ['required', 'string', 'max:100'],
            'jenis_dokumen'   => ['required', 'string', Rule::in([
                'KSB', 'Nota Kesepakatan', 'Perjanjian Teknis', 'PKS', 'Rencana Kerja', 'MOU', 'RKT', 'LOI',
            ])],
            'judul'           => ['required', 'string', 'max:255'],
            'nama_pihak_luar' => ['required', 'string', 'max:255'],
            'nomor_suratM'    => ['required', 'string', 'max:100'],
            'pembiayaan'      => ['required', 'string', Rule::in([
                'APBN', 'APBD', 'PIHAK KETIGA', 'PARA PIHAK', 'SESUAI DENGAN PERATURAN PERUNDANG-UNDANGAN',
            ])],
            
            // PERBAIKAN: Memanggil static method yang mengembalikan array string murni
            'urusan'          => ['required', 'string', Rule::in(UrusanEnum::cases())],
            'tanggal_mulai'   => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after:tanggal_mulai'],
            'dokumen_file'    => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ];
    }
}
<?php

namespace App\Http\Requests\Mitra;

use DateTimeImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Throwable;

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
            'jenis_dokumen' => ['required', 'string', Rule::in([
                'KSB',
                'Nota Kesepakatan',
                'Perjanjian Teknis',
                'PKS',
                'Rencana Kerja',
                'MOU',
                'RKT',
                'LOI',
            ])],
            'judul' => ['required', 'string', 'max:255'],
            'nama_pihak_luar' => ['required', 'string', 'max:255'],
            'nomor_suratM' => ['required', 'string', 'max:100'],
            'pembiayaan' => ['required', 'string', Rule::in([
                'APBN',
                'APBD',
                'PIHAK KETIGA',
                'PARA PIHAK',
                'SESUAI DENGAN PERATURAN PERUNDANG-UNDANGAN',
            ])],
            'urusan' => ['required', 'string', 'max:255'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date'],
            'dokumen_file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (! $this->filled('tanggal_mulai') || ! $this->filled('tanggal_selesai')) {
                return;
            }

            try {
                $mulai = new DateTimeImmutable($this->string('tanggal_mulai')->toString());
                $selesai = new DateTimeImmutable($this->string('tanggal_selesai')->toString());
            } catch (Throwable) {
                $validator->errors()->add('tanggal_selesai', 'Format tanggal tidak valid.');

                return;
            }

            if ($selesai <= $mulai) {
                $validator->errors()->add('tanggal_selesai', 'Tanggal selesai harus setelah tanggal mulai.');
            }
        });
    }
}

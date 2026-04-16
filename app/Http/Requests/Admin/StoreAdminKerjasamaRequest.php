<?php

namespace App\Http\Requests\Admin;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreAdminKerjasamaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'id_mitra' => ['required', 'integer', 'exists:mitras,id_mitra'],
            'tahun' => ['required', 'integer', 'min:1900', 'max:2100'],
            'judul' => ['required', 'string', 'max:255'],
            'jangka_waktu_bulan' => ['required', 'integer', 'min:1'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after:tanggal_mulai'],
            'dokumen_file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'nomor_suratP' => ['nullable', 'string', 'max:100'],
            'jenis_kerjasama' => ['nullable', 'string', 'max:100'],
            'jenis_dokumen' => ['nullable', 'string', 'max:100'],
            'urusan' => ['nullable', 'string', 'max:255'],
            'daerah' => ['nullable', 'string', 'max:255'],
            'id_kategori' => ['nullable', 'integer', 'exists:kategori_kerjasama,id_kategori'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (! $this->filled('tanggal_mulai') || ! $this->filled('tanggal_selesai')) {
                return;
            }

            $start = Carbon::parse($this->input('tanggal_mulai'));
            $end = Carbon::parse($this->input('tanggal_selesai'));

            if ((int) $this->input('tahun') !== $start->year) {
                $validator->errors()->add('tahun', 'Tahun kerjasama harus sama dengan tahun tanggal mulai.');
            }

            $actualMonths = $start->diffInMonths($end);
            if ((int) $this->input('jangka_waktu_bulan') !== $actualMonths) {
                $validator->errors()->add('jangka_waktu_bulan', 'Jangka waktu tidak sesuai dengan rentang tanggal mulai dan selesai.');
            }
        });
    }
}

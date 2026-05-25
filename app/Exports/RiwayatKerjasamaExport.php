<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RiwayatKerjasamaExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private Collection $rows)
    {
    }

    public function collection(): Collection
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'No',
            'Tahun',
            'Tipe',
            'Pemrakarsa',
            'Mitra/Pihak',
            'Judul',
            'Tanggal Mulai',
            'Tanggal Berakhir',
            'Jangka Waktu',
            'Status',
            'Jenis Kerjasama',
            'Jenis Dokumen',
            'Nomor Surat Mitra',
            'Nomor Surat Pemerintah',
            'Urusan',
            'Pembiayaan',
        ];
    }

    public function map($row): array
    {
        $row = (array) $row;

        return [
            $row['no'] ?? '',
            $row['tahun'] ?? '',
            $row['tipe'] ?? '',
            $row['pemrakarsa'] ?? '',
            $row['mitra'] ?? '',
            $row['judul'] ?? '',
            $row['tanggal_mulai'] ?? '',
            $row['tanggal_berakhir'] ?? '',
            $row['jangka_waktu'] ?? '',
            $row['status'] ?? '',
            $row['jenis_kerjasama'] ?? '',
            $row['jenis_dokumen'] ?? '',
            $row['nomor_suratM'] ?? '',
            $row['nomor_suratP'] ?? '',
            $row['urusan'] ?? '',
            $row['pembiayaan'] ?? '',
        ];
    }
}

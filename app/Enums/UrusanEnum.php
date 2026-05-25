<?php

namespace App\Enums;

class UrusanEnum
{
    public static function cases(): array
    {
        return [
            'SEMUA URUSAN',
            'PENDIDIKAN',
            'KESEHATAN',
            'PEKERJAAN UMUM DAN PENATAAN RUANG',
            'PERUMAHAN RAKYAT DAN KAWASAN PERMUKIMAN',
            'KETENTRAMAN, KETERTIBAN UMUM DAN PERLINDUNGAN MASYARAKAT',
            'SOSIAL',
            'TENAGA KERJA',
            'PEMBERDAYAAN PEREMPUAN DAN PERLINDUNGAN ANAK',
            'PANGAN',
            'PERTANAHAN',
        ];
    }

    public static function options(): array
    {
        return array_map(fn($case) => ['value' => $case, 'label' => $case], self::cases());
    }
}

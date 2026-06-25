<?php

namespace App\Support;

class JenisKerjasama
{
    private const DATA = [
        'KSDD' => 'Kerjasama Daerah Antar Daerah (KSDD)',

        'KSDPK' => 'Kerjasama Dengan Pihak Ketiga (KSDPK)',

        'NK/RK' => 'Sinergi Dengan Pemerintah Pusat / Lembaga (NK/RK)',

        'PERTEK' => 'Perjanjian Teknis (PERTEK)',

        'KSDPL' => 'Kerjasama Daerah Dengan Pemerintah Daerah Di Luar Negeri (KSDPL)',

        'KSDLL' => 'Kerjasama Daerah Dengan Lembaga Di Luar Negeri (KSDLL)',
    ];

    public static function toCode(string $label): string
    {
        foreach (self::DATA as $code => $fullName) {

            if ($fullName === trim($label)) {
                return $code;
            }

        }

        return $label;
    }

    public static function toLabel(string $code): string
    {
        return self::DATA[$code] ?? $code;
    }

    public static function options(): array
    {
        return self::DATA;
    }
}
<?php

namespace App\Enums;

enum StatusPersetujuan: string
{
    case Disetujui = 'disetujui';
    case Revisi    = 'revisi';
    case Dibatalkan = 'dibatalkan';

    public function label(): string
    {
        return match ($this) {
            self::Disetujui => 'Disetujui',
            self::Revisi    => 'Revisi',
            self::Dibatalkan => 'Dibatalkan',
        };
    }
}

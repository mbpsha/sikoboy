<?php

namespace App\Enums;

enum StatusPersetujuan: string
{
    case Disetujui = 'disetujui';
    case Revisi    = 'revisi';
    case Ditolak   = 'ditolak';

    public function label(): string
    {
        return match ($this) {
            self::Disetujui => 'Disetujui',
            self::Revisi    => 'Revisi',
            self::Ditolak   => 'Ditolak',
        };
    }
}

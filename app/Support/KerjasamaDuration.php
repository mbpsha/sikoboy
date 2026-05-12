<?php

namespace App\Support;

use Carbon\Carbon;
use DateTimeInterface;

class KerjasamaDuration
{
    public static function months(DateTimeInterface|string $mulai, DateTimeInterface|string $berakhir): int
    {
        return Carbon::parse($mulai)->diffInMonths(Carbon::parse($berakhir));
    }
}

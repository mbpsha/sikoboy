<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodeKerjasama extends Model
{
    use HasFactory;

    protected $table = 'periode_kerjasama';
    protected $primaryKey = 'id_periode';
    public $timestamps = false;

    /**
     * @var array<string>
     */
    protected $fillable = [
        'id_kerjasama',
        'tanggal_mulai',
        'tanggal_berakhir',
        'keterangan',
    ];

    protected static function booted(): void
    {
        static::saved(function (self $periode): void {
            $periode->syncKerjasamaJangkaWaktu();
        });

        static::deleted(function (self $periode): void {
            $periode->syncKerjasamaJangkaWaktu();
        });
    }

    public function kerjasama()
    {
        return $this->belongsTo(Kerjasama::class, 'id_kerjasama', 'id_kerjasama');
    }

    private function syncKerjasamaJangkaWaktu(): void
    {
        if ($this->id_kerjasama === null) {
            return;
        }

        $latestPeriode = self::query()
            ->where('id_kerjasama', $this->id_kerjasama)
            ->orderByDesc('tanggal_berakhir')
            ->orderByDesc('id_periode')
            ->first(['tanggal_mulai', 'tanggal_berakhir']);

        if ($latestPeriode === null) {
            Kerjasama::query()
                ->where('id_kerjasama', $this->id_kerjasama)
                ->update(['jangka_waktu' => null]);

            return;
        }

        $jangkaWaktu = Carbon::parse($latestPeriode->tanggal_mulai)
            ->diffInMonths(Carbon::parse($latestPeriode->tanggal_berakhir));

        Kerjasama::query()
            ->where('id_kerjasama', $this->id_kerjasama)
            ->update(['jangka_waktu' => $jangkaWaktu]);
    }
}

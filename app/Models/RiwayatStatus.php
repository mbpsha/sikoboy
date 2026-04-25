<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatStatus extends Model
{
    use HasFactory;

    protected $table = 'riwayat_status';
    protected $primaryKey = 'id_riwayat';
    public $timestamps = false;

    /**
     * @var array<string>
     */
    protected $fillable = [
        'id_kerjasama',
        'id_status',
        'id_admin',
        'catatan',
        'tanggal',
    ];

    protected static function booted(): void
    {
        static::created(function (self $riwayat): void {
            $riwayat->loadMissing('status');
            $jenisStatus = $riwayat->status?->jenis_status;

            if (! in_array($jenisStatus, ['disetujui', 'revisi', 'ditolak'], true)) {
                return;
            }

            Kerjasama::query()
                ->whereKey($riwayat->id_kerjasama)
                ->update(['status_persetujuan' => $jenisStatus]);
        });
    }

    public static function recordStatus(
        int $idKerjasama,
        string $jenisStatus,
        int $idAdmin,
        ?string $catatan = null
    ): self {
        $status = Status::query()->firstOrCreate(['jenis_status' => $jenisStatus]);

        return self::create([
            'id_kerjasama' => $idKerjasama,
            'id_status' => $status->id_status,
            'id_admin' => $idAdmin,
            'catatan' => $catatan ?? '-',
            'tanggal' => now(),
        ]);
    }

    public function kerjasama()
    {
        return $this->belongsTo(Kerjasama::class, 'id_kerjasama', 'id_kerjasama');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'id_status', 'id_status');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admin', 'id_admin');
    }
}

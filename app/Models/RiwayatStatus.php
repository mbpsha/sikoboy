<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatStatus extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    public const SNAPSHOT_SYNCABLE_STATUSES = ['disetujui', 'revisi', 'dibatalkan'];

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

    /**
     * Record status transition and sync kerjasama status snapshot when applicable.
     */
    public static function recordStatus(
        int $idKerjasama,
        string $jenisStatus,
        int $idAdmin,
        ?string $catatan = null
    ): self {
        $status = Status::query()->firstOrCreate(['jenis_status' => $jenisStatus]);
        $riwayat = self::create([
            'id_kerjasama' => $idKerjasama,
            'id_status' => $status->id_status,
            'id_admin' => $idAdmin,
            'catatan' => $catatan ?? '-',
            'tanggal' => now(),
        ]);

        if (in_array($jenisStatus, self::SNAPSHOT_SYNCABLE_STATUSES, true)) {
            Kerjasama::query()
                ->whereKey($idKerjasama)
                ->update(['status_persetujuan' => $jenisStatus]);
        }

        return $riwayat;
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

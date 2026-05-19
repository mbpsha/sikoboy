<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin;
use Illuminate\Support\Facades\Schema;

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
        'judul',
        'penanggung_jawab',
        'file',
        'tanggal',
    ];

    /**
     * Record status transition and sync kerjasama status snapshot when applicable.
     */
    public static function recordStatus(
        int $idKerjasama,
        string $jenisStatus,
        ?int $idAdmin = null,
        ?string $catatan = null,
        ?string $penanggungJawab = null,
        ?string $judul = null,
        ?string $file = null
    ): self {
        $status = Status::query()->firstOrCreate(['jenis_status' => $jenisStatus]);
        // If an admin id is provided but no penanggungJawab string, prefer storing the admin's divisi
        if ($idAdmin !== null && empty($penanggungJawab)) {
            $admin = Admin::find($idAdmin);
            if ($admin) {
                $penanggungJawab = $admin->divisi ?? null;
            }
        }
        $data = [
            'id_kerjasama' => $idKerjasama,
            'id_status' => $status->id_status,
            'id_admin' => $idAdmin ?: null,
            // store catatan and a separate judul (title) if provided
            'catatan' => $catatan ?? null,
            'judul' => $judul ?? null,
            'penanggung_jawab' => $penanggungJawab,
            'tanggal' => now(),
        ];

        // Only include 'file' if the database column exists (migration may not have run yet)
        if (Schema::hasColumn('riwayat_status', 'file')) {
            $data['file'] = $file ?? null;
        }

        $riwayat = self::create($data);

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

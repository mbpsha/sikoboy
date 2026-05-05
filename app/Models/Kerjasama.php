<?php

namespace App\Models;

use App\Enums\StatusPersetujuan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Kerjasama extends Model
{
    use HasFactory;

    protected $table = 'kerjasama';

    protected $primaryKey = 'id_kerjasama';

    public $timestamps = false;

    /**
     * @var array<string>
     */
    protected $fillable = [
        'id_mitra',
        'id_admin',
        'id_kategori',
        'judul',
        'nomor_suratM',
        'nomor_suratP',
        'urusan',
        'daerah',
        'status_aktif',
        'pembiayaan',
        'pemrakarsa',
        'jenis_kerjasama',
        'jenis_dokumen',
        'tipe',
        'nama_pihak_luar',
        'is_finalized',
        'status_negosiasi',
        'status_persetujuan',
        'catatan_persetujuan',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_finalized' => 'boolean',
            'created_at' => 'datetime',
            'status_persetujuan' => StatusPersetujuan::class,
        ];
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function mitra()
    {
        return $this->belongsTo(Mitra::class, 'id_mitra', 'id_mitra');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admin', 'id_admin');
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriKerjasama::class, 'id_kategori', 'id_kategori');
    }

    public function periodes()
    {
        return $this->hasMany(PeriodeKerjasama::class, 'id_kerjasama', 'id_kerjasama');
    }

    public function latestPeriode()
    {
        return $this->hasOne(PeriodeKerjasama::class, 'id_kerjasama', 'id_kerjasama')
            ->latestOfMany('tanggal_berakhir');
    }

    public function dokumen()
    {
        return $this->hasMany(Dokumen::class, 'id_kerjasama', 'id_kerjasama');
    }

    public function finalDokumen()
    {
        return $this->hasOne(Dokumen::class, 'id_kerjasama', 'id_kerjasama')
            ->latestOfMany('versi_dokumen');
    }

    public function riwayatStatus()
    {
        return $this->hasMany(RiwayatStatus::class, 'id_kerjasama', 'id_kerjasama');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeFinalized(Builder $query): Builder
    {
        return $query->where('is_finalized', true);
    }

    public function scopeMitraTipe(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->where('pemrakarsa', 'M')
                ->orWhere(function (Builder $legacy) {
                    $legacy->whereNull('pemrakarsa')->where('tipe', 'mitra');
                });
        });
    }

    public function scopePemerintahTipe(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->where('pemrakarsa', 'P')
                ->orWhere(function (Builder $legacy) {
                    $legacy->whereNull('pemrakarsa')->where('tipe', 'pemerintah');
                });
        });
    }

    public function scopeAktif(Builder $query): Builder
    {
        $today = Carbon::today()->toDateString();

        return $query->whereHas('latestPeriode', function (Builder $q) use ($today) {
            $q->where('tanggal_mulai', '<=', $today)
                ->where('tanggal_berakhir', '>=', $today);
        });
    }

    public function scopeAkanBerakhir(Builder $query, int $days = 30): Builder
    {
        $today = Carbon::today()->toDateString();
        $threshold = Carbon::today()->addDays($days)->toDateString();

        return $query->whereHas('latestPeriode', function (Builder $q) use ($today, $threshold) {
            $q->where('tanggal_berakhir', '>', $today)
                ->where('tanggal_berakhir', '<=', $threshold);
        });
    }

    public function scopeBerakhir(Builder $query): Builder
    {
        $today = Carbon::today()->toDateString();

        return $query->whereHas('latestPeriode', function (Builder $q) use ($today) {
            $q->where('tanggal_berakhir', '<', $today);
        });
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    /**
     * Dynamically compute the active status label.
     */
    public function getStatusLabelAttribute(): ?string
    {
        if ($this->pemrakarsa === 'M' && $this->status_persetujuan !== StatusPersetujuan::Disetujui) {
            return null;
        }

        $today = Carbon::today();
        $threshold = Carbon::today()->addMonths(3);

        $periode = $this->latestPeriode;

        if (! $periode) {
            return null;
        }

        $berakhir = Carbon::parse($periode->tanggal_berakhir);

        if ($berakhir->lt($today)) {
            return 'berakhir';
        }

        if ($berakhir->lte($threshold)) {
            return 'segera berakhir';
        }

        return 'aktif';
    }

    public function getNomorSuratAttribute(): ?string
    {
        return $this->pemrakarsa === 'P'
            ? ($this->nomor_suratP ?? $this->nomor_suratM)
            : ($this->nomor_suratM ?? $this->nomor_suratP);
    }
}

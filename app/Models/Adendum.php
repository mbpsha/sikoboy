<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Adendum extends Model
{
    use HasFactory;

    protected $table = 'adendum';
    protected $primaryKey = 'id_adendum';
    public $timestamps = false;

    /**
     * @var array<string>
     */
    protected $fillable = [
        'id_kerjasama',
        'mitra',
        'tahun',
        'judul_adendum',
        'keterangan_adendum',
        'nomor_surat_mitra_baru',
        'nomor_surat_pemerintah_baru',
        'nomor_surat_mitra_lama',
        'nomor_surat_pemerintah_lama',
        'urusan',
        'jangka_waktu',
        'jenis_kerjasama',
        'tanggal_mulai',
        'tanggal_berakhir',
        'pembiayaan',
        'nama_file',
        'lokasi_file',
        'created_by',
    ];

    public function kerjasama()
    {
        return $this->belongsTo(Kerjasama::class, 'id_kerjasama', 'id_kerjasama');
    }
}

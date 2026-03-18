<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'nomor_surat',
        'urusan',
        'daerah',
        'status_aktif',
    ];

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
}

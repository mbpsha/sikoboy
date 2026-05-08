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
        'judul_adendum',
        'keterangan_adendum',
        'nama_file',
        'lokasi_file',
        'created_by',
    ];

    public function kerjasama()
    {
        return $this->belongsTo(Kerjasama::class, 'id_kerjasama', 'id_kerjasama');
    }
}

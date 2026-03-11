<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mitra extends Model
{
    use HasFactory;

    protected $table = 'mitras';
    protected $primaryKey = 'id_user';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'id_user',
        'nama_perusahaan',
        'npwp',
        'pic',
        'jabatan_pic',
        'no_handphone',
        'no_telepon',
        'alamat',
        'provinsi',
        'kabupaten_kota',
        'kecamatan',
        'kode_pos',
        'bidang_usaha',
        'website',
        'logo_perusahaan',
    ];

    /**
     * Get the user that owns the mitra profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}

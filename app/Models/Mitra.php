<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mitra extends Model
{
    use HasFactory;

    protected $table = 'mitras';
    protected $primaryKey = 'id_mitra';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'id_user',
        'nama_perusahaan',
        'npwp',
        'no_handphone',
        'pic',
        'alamat',
    ];

    /**
     * Get the user that owns the mitra profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}

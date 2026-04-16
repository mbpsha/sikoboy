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

    /**
     * All kerjasama records belonging to this mitra.
     */
    public function kerjasama()
    {
        return $this->hasMany(Kerjasama::class, 'id_mitra', 'id_mitra');
    }
}

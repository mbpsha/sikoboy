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

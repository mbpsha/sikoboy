<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodeKerjasama extends Model
{
    use HasFactory;

    protected $table = 'periode_kerjasama';
    protected $primaryKey = 'id_periode';
    public $timestamps = false;

    /**
     * @var array<string>
     */
    protected $fillable = [
        'id_kerjasama',
        'tanggal_mulai',
        'tanggal_berakhir',
        'keterangan',
    ];

    public function kerjasama()
    {
        return $this->belongsTo(Kerjasama::class, 'id_kerjasama', 'id_kerjasama');
    }
}

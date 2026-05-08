<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PotensiPoin extends Model
{
    use HasFactory;

    protected $table = 'potensi_poin';

    protected $primaryKey = 'id_potensi_poin';

    public $timestamps = false;

    protected $fillable = [
        'id_potensi',
        'isi',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function potensi()
    {
        return $this->belongsTo(Potensi::class, 'id_potensi', 'id_potensi');
    }
}

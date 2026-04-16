<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Potensi extends Model
{
    use HasFactory;

    protected $table = 'potensi';

    protected $primaryKey = 'id_potensi';

    public $timestamps = false;

    protected $fillable = [
        'kategori',
        'judul',
        'deskripsi',
        'gambar_path',
        'status_tampil',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'status_tampil' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function poin()
    {
        return $this->hasMany(PotensiPoin::class, 'id_potensi', 'id_potensi')
            ->orderBy('urutan');
    }
}

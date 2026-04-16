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
        'judul',
        'deskripsi',
        'status_tampil',
    ];

    protected function casts(): array
    {
        return [
            'status_tampil' => 'boolean',
            'created_at' => 'datetime',
        ];
    }
}

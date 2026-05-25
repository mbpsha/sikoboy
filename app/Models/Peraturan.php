<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peraturan extends Model
{
    protected $fillable = [
        'judul',
        'file',
        'nama_file',
        'thumbnail',
    ];
}
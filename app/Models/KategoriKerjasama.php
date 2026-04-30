<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriKerjasama extends Model
{
    use HasFactory;

    protected $table = 'kategori_kerjasama';
    protected $primaryKey = 'id_kategori';
    public $timestamps = false;

    /**
     * @var array<string>
     */
    protected $fillable = [
        'nama_kategori',
        'deskripsi',
        'file_template',
    ];

    public function templates()
    {
        return $this->hasMany(TemplateDokumen::class, 'id_kategori', 'id_kategori');
    }
}

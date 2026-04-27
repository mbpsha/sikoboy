<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateDokumen extends Model
{
    use HasFactory;

    protected $table = 'template_dokumen';

    protected $primaryKey = 'id_template_dokumen';

    public $timestamps = false;

    protected $fillable = [
        'id_admin',
        'id_kategori',
        'nama_file',
        'jenis_dokumen',
        'lokasi_file',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admin', 'id_admin');
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriKerjasama::class, 'id_kategori', 'id_kategori');
    }
}

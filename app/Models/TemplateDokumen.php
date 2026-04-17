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
        'nama_file',
        'lokasi_file',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admin', 'id_admin');
    }
}

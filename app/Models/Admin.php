<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;

    protected $table = 'admins';
    protected $primaryKey = 'id_admin';
    public $timestamps = false;

    /**
     * @var array<string>
     */
    protected $fillable = [
        'id_user',
        'nama',
        'divisi',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * All kerjasama records managed by this admin.
     */
    public function kerjasama()
    {
        return $this->hasMany(Kerjasama::class, 'id_admin', 'id_admin');
    }
}

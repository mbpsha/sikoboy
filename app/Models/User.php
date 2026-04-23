<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id_user';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'password',
        'role',
        'status_verifikasi',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function admin()
    {
        return $this->hasOne(Admin::class, 'id_user', 'id_user');
    }

    public function mitra()
    {
        return $this->hasOne(Mitra::class, 'id_user', 'id_user');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isMitra(): bool
    {
        return $this->role === 'mitra';
    }

    public function isMitraVerified(): bool
    {
        if (! $this->isMitra()) {
            return true;
        }

        return $this->status_verifikasi === 'disetujui';
    }

    public function hasCompleteProfile(): bool
    {
        if ($this->isAdmin()) {
            return $this->admin !== null;
        }
        if ($this->isMitra()) {
            return $this->mitra !== null;
        }
        return false;
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->admin) {
            return $this->admin->nama;
        }
        if ($this->mitra) {
            return $this->mitra->pic;
        }
        return $this->email;
    }
}

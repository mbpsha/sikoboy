<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
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
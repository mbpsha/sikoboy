<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'password',
        'role',
        'email_verified_at',
        'is_active',
        'last_login_at',
        'last_login_ip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the admin profile associated with the user.
     */
    public function admin()
    {
        return $this->hasOne(Admin::class, 'id_user', 'id_user');
    }

    /**
     * Get the mitra profile associated with the user.
     */
    public function mitra()
    {
        return $this->hasOne(Mitra::class, 'id_user', 'id_user');
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is mitra.
     */
    public function isMitra(): bool
    {
        return $this->role === 'mitra';
    }

    /**
     * Check if user has complete profile.
     */
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

    /**
     * Get display name attribute.
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->isAdmin() && $this->admin) {
            return $this->admin->nama_lengkap;
        }
        if ($this->isMitra() && $this->mitra) {
            return $this->mitra->pic;
        }
        return $this->email;
    }
}

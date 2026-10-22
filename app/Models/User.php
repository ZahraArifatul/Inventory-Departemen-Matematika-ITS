<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    public const ROLES = ['admin' => 'Admin', 'teknisi' => 'Teknisi', 'manajemen' => 'Manajemen'];

    protected $fillable = ['name', 'email', 'password', 'role', 'aktif'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = ['email_verified_at' => 'datetime', 'aktif' => 'boolean'];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /** Admin & Teknisi: boleh input/ubah data operasional. */
    public function bisaKelola(): bool
    {
        return in_array($this->role, ['admin', 'teknisi'], true);
    }

    /** Admin & Manajemen: boleh membuat laporan / ekspor. */
    public function bisaLaporan(): bool
    {
        return in_array($this->role, ['admin', 'manajemen'], true);
    }

    public function getLabelRoleAttribute(): string
    {
        return self::ROLES[$this->role] ?? $this->role;
    }
}

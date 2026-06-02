<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'google_id',   // ← untuk OAuth Google
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /**
     * URL avatar: pakai yang sudah disimpan, atau generate dari inisial nama.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            // Kalau URL lengkap (dari Google), pakai langsung
            if (str_starts_with($this->avatar, 'http')) {
                return $this->avatar;
            }
            // Kalau path lokal (upload manual), pakai storage
            return asset('storage/' . $this->avatar);
        }

        // Fallback: generate avatar dari inisial nama
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name)
            . '&background=b30084&color=fff&size=128&bold=true&format=svg';
    }

    /**
     * Cek apakah user login via Google (tidak punya password manual).
     */
    public function isGoogleUser(): bool
    {
        return ! is_null($this->google_id);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}

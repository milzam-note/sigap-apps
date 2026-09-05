<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// Update attribute Fillable agar selaras dengan properti $fillable di bawah
#[Fillable(['name', 'nrp_nip', 'jabatan', 'pangkat', 'email', 'password', 'role', 'is_active', 'email_verified_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'nrp_nip',
        'jabatan',
        'pangkat',
        'email',
        'password',
        'role',              // Pastikan role terdaftar agar bisa di-update
        'is_active',         // Penambahan status akun (aktif/nonaktif)
        'email_verified_at', // Tambahkan ini agar bisa diverifikasi otomatis
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
            'password' => 'hashed',
            'is_active' => 'boolean', // Memastikan data dibaca sebagai true/false
        ];
    }
}

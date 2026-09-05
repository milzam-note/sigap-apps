<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Surat extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Relasi ke Jenis Surat
    public function jenisSurat()
    {
        return $this->belongsTo(JenisSurat::class, 'jenis_surat_id');
    }

    // Relasi ke User (Pembuat Pertama)
    public function pembuat()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relasi ke User (Pengubah Terakhir)
    public function pengubah()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Relasi ke Riwayat Surat (Bisa banyak riwayat)
    public function riwayat()
    {
        return $this->hasMany(RiwayatSurat::class, 'surat_id')->latest();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatSurat extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Kembali ke Surat Utama
    public function surat()
    {
        return $this->belongsTo(Surat::class, 'surat_id');
    }

    // Relasi ke Jenis Surat (Versi Lama)
    public function jenisSuratLama()
    {
        return $this->belongsTo(JenisSurat::class, 'jenis_surat_id_lama');
    }

    // Relasi ke User yang melakukan perubahan
    public function pengubah()
    {
        return $this->belongsTo(User::class, 'diubah_oleh');
    }
}

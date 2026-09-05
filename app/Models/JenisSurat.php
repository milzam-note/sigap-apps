<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisSurat extends Model
{
    use HasFactory;

    // Mengizinkan semua kolom diisi secara massal kecuali 'id'
    protected $guarded = ['id'];

    // Relasi: 1 Jenis Surat memiliki Banyak (hasMany) Surat
    public function surats()
    {
        return $this->hasMany(Surat::class);
    }
}

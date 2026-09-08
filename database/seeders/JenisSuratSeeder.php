<?php

namespace Database\Seeders;

use App\Models\JenisSurat;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JenisSuratSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        JenisSurat::create(['nama_jenis' => 'Undang-Undang']);
        JenisSurat::create(['nama_jenis' => 'Peraturan Menteri Keuangan']);
        JenisSurat::create(['nama_jenis' => 'Keputusan Kapolri']);
        JenisSurat::create(['nama_jenis' => 'Perdirjen Anggaran']);
        JenisSurat::create(['nama_jenis' => 'Peraturan Dirjen Perbendaharaan']);
        JenisSurat::create(['nama_jenis' => 'Peraturan Dirjen Perbendaharaan']);
        JenisSurat::create(['nama_jenis' => 'Keputusan As SDM Kapolri']);
        JenisSurat::create(['nama_jenis' => 'Dokumen Perencanaan Anggaran']);
        JenisSurat::create(['nama_jenis' => 'Dokumen Anev Anggaran']);
    }
}

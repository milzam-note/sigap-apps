<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_surats', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel surat utama
            $table->foreignId('surat_id')->constrained('surats')->onDelete('cascade');

            // Kolom untuk menampung data versi lama
            $table->string('nama_surat_lama');
            $table->date('tanggal_surat_lama');
            $table->string('nomor_surat_lama');
            $table->foreignId('jenis_surat_id_lama')->constrained('jenis_surats')->onDelete('cascade');
            $table->enum('sifat_surat_lama', ['umum', 'rahasia']);
            $table->string('file_pdf_lama'); // Menyimpan path file PDF versi lama

            // Pencatat siapa yang melakukan perubahan (mengubah versi)
            $table->foreignId('diubah_oleh')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            // Kolom created_at di tabel ini otomatis menjadi penanda waktu kapan perubahan/revisi dilakukan.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_surats');
    }
};

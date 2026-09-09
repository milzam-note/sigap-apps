<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('surats', function (Blueprint $table) {
            $table->id();
            $table->string('nama_surat');
            $table->date('tanggal_surat');
            $table->text('deskripsi');
            $table->string('tahun_dokumen', 4);
            $table->string('nomor_surat');

            // Relasi ke Jenis Surat
            $table->foreignId('jenis_surat_id')
                ->constrained('jenis_surats')
                ->onDelete('cascade');

            // Klasifikasi Kerahasiaan Surat
            $table->enum('sifat_surat', ['umum', 'rahasia'])->default('umum');

            // Jumlah melihat dan mengunduh
            $table->integer('jumlah_dilihat')->default(0);
            $table->integer('jumlah_unduh')->default(0);

            $table->string('file_pdf');

            // Log Activity
            // nullOnDelete() digunakan agar jika user admin dihapus, data surat tidak ikut terhapus, hanya status 'created_by' nya menjadi kosong.
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surats');
    }
};

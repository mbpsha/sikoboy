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
        Schema::create('kerjasama', function (Blueprint $table) {
            $table->id('id_kerjasama');
            $table->unsignedBigInteger('id_mitra');
            $table->unsignedBigInteger('id_admin');
            $table->unsignedBigInteger('id_kategori');
            $table->string('judul');
            $table->string('nomor_surat');
            $table->enum('urusan', [
                'SEMUA URUSAN',
                'PENDIDIKAN',
                'KESEHATAN',
                'PEKERJAAN UMUM DAN PENATAAN RUANG',
                'PERUMAHAN RAKYAT DAN KAWASAN PERMUKIMAN',
                'KETENTRAMAN, KETERTIBAN UMUM DAN PERLINDUNGAN MASYARAKAT',
                'SOSIAL',
                'TENAGA KERJA',
                'PEMBERDAYAAN PEREMPUAN DAN PERLINDUNGAN ANAK',
                'PANGAN',
                'PERTANAHAN',
            ]);
            $table->string('daerah');
            $table->string('status_aktif');
            $table->timestamp('created_at')->useCurrent();
            $table->enum('pembiayaan', [
                'APBN',
                'APBD',
                'PIHAK KETIGA',
                'PARA PIHAK',
                'SESUAI DENGAN PERATURAN PERUNDANG-UNDANGAN',
            ]);
            $table->foreign('id_mitra')->references('id_mitra')->on('mitras')->onDelete('cascade');
            $table->foreign('id_admin')->references('id_admin')->on('admins')->onDelete('cascade');
            $table->foreign('id_kategori')->references('id_kategori')->on('kategori_kerjasama')->onDelete('restrict');
            $table->index(['id_mitra', 'id_admin']);
            $table->index('id_kategori');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kerjasama');
    }
};

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
        Schema::create('adendum', function (Blueprint $table) {
            $table->id('id_adendum');
            $table->unsignedBigInteger('id_kerjasama');
            $table->string('mitra')->nullable();
            $table->string('tahun', 4)->nullable();
            $table->string('judul_adendum');
            $table->string('nomor_surat_mitra_baru')->nullable();
            $table->string('nomor_surat_pemerintah_baru')->nullable();
            $table->string('nomor_surat_mitra_lama')->nullable();
            $table->string('nomor_surat_pemerintah_lama')->nullable();
            $table->string('urusan')->nullable();
            $table->string('jangka_waktu')->nullable();
            $table->string('jenis_kerjasama')->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_berakhir')->nullable();
            $table->text('pembiayaan')->nullable();
            $table->text('keterangan_adendum')->nullable();
            $table->string('nama_file');
            $table->string('lokasi_file');
            $table->unsignedBigInteger('created_by');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('id_kerjasama')->references('id_kerjasama')->on('kerjasama')->onDelete('cascade');
            $table->index('id_kerjasama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adendum');
    }
};

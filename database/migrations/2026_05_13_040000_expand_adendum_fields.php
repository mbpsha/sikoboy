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
        Schema::table('adendum', function (Blueprint $table) {
            $table->string('mitra')->nullable()->after('id_kerjasama');
            $table->string('tahun', 4)->nullable()->after('mitra');
            $table->string('nomor_surat_mitra_baru')->nullable()->after('judul_adendum');
            $table->string('nomor_surat_pemerintah_baru')->nullable()->after('nomor_surat_mitra_baru');
            $table->string('nomor_surat_mitra_lama')->nullable()->after('nomor_surat_pemerintah_baru');
            $table->string('nomor_surat_pemerintah_lama')->nullable()->after('nomor_surat_mitra_lama');
            $table->string('urusan')->nullable()->after('nomor_surat_pemerintah_lama');
            $table->string('jangka_waktu')->nullable()->after('urusan');
            $table->string('jenis_kerjasama')->nullable()->after('jangka_waktu');
            $table->date('tanggal_mulai')->nullable()->after('jenis_kerjasama');
            $table->date('tanggal_berakhir')->nullable()->after('tanggal_mulai');
            $table->text('pembiayaan')->nullable()->after('tanggal_berakhir');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('adendum', function (Blueprint $table) {
            $table->dropColumn([
                'mitra',
                'tahun',
                'nomor_surat_mitra_baru',
                'nomor_surat_pemerintah_baru',
                'nomor_surat_mitra_lama',
                'nomor_surat_pemerintah_lama',
                'urusan',
                'jangka_waktu',
                'jenis_kerjasama',
                'tanggal_mulai',
                'tanggal_berakhir',
                'pembiayaan',
            ]);
        });
    }
};

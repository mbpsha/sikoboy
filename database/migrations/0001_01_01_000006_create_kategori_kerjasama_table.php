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
        Schema::create('kategori_kerjasama', function (Blueprint $table) {
            $table->id('id_kategori');
                        $table->enum('nama_kategori', [
                'Kerjasama Daerah Antar Daerah (KSDD)',
                'Kerjasama Dengan Pihak Ketiga (KSDPK)',
                'Sinergi Dengan Pemerintah Pusat / Lembaga (NK/RK)',
                'Perjanjian Teknis (PERTEK)',
                'Kerjasama Daerah Dengan Pemerintah Daerah Di Luar Negeri (KSDPL)',
                'Kerjasama Daerah Dengan Lembaga Di Luar Negeri (KSDLL)',
            ]);
            $table->string('deskripsi');
            $table->string('file_template');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori_kerjasama');
    }
};

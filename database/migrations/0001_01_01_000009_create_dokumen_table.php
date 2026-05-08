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
        Schema::create('dokumen', function (Blueprint $table) {
            $table->id('id_dokumen');
                        $table->unsignedBigInteger('id_kerjasama');
            $table->enum('jenis_dokumen', [
                'KSB',
                'Nota Kesepakatan',
                'Perjanjian Teknis',
                'PKS',
                'Rencana Kerja',
                'MOU',
                'RKT',
                'LOI',
            ]);
            $table->string('nama_file');
            $table->string('lokasi_file');
            $table->integer('versi_dokumen');
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
        Schema::dropIfExists('dokumen');
    }
};

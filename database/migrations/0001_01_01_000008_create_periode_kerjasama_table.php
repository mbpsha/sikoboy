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
        Schema::create('periode_kerjasama', function (Blueprint $table) {
            $table->id('id_periode');
            $table->unsignedBigInteger('id_kerjasama');
            $table->date('tanggal_mulai');
            $table->date('tanggal_berakhir');
            $table->string('keterangan');

            $table->foreign('id_kerjasama')->references('id_kerjasama')->on('kerjasama')->onDelete('cascade');
            $table->index('id_kerjasama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periode_kerjasama');
    }
};

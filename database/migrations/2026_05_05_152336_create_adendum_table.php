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
            $table->string('judul_adendum');
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
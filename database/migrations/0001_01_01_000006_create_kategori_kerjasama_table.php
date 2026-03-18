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
            $table->string('nama_kategori');
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

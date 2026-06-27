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
        Schema::create('potensi', function (Blueprint $table) {
            $table->id('id_potensi');
            $table->string('kategori')->nullable();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->string('gambar_path')->nullable();
            $table->boolean('status_tampil')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();

            $table->index('kategori');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('potensi');
    }
};

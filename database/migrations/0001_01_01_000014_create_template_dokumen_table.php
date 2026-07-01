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
        Schema::create('template_dokumen', function (Blueprint $table) {
            $table->id('id_template_dokumen');
            $table->unsignedBigInteger('id_admin');
            $table->unsignedBigInteger('id_kategori')->nullable();
            $table->string('judul')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('nama_file');
            $table->string('jenis_dokumen')->nullable();
            $table->string('lokasi_file');
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('id_admin')->references('id_admin')->on('admins')->onDelete('cascade');
            $table->foreign('id_kategori')->references('id_kategori')->on('kategori_kerjasama')->onDelete('set null');
            $table->index(['id_kategori', 'is_active'], 'template_dokumen_kategori_active_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_dokumen');
    }
};

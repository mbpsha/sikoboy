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
        Schema::table('template_dokumen', function (Blueprint $table) {
            $table->unsignedBigInteger('id_kategori')->nullable()->after('id_admin');
            $table->string('jenis_dokumen')->nullable()->after('nama_file');
            $table->boolean('is_active')->default(true)->after('lokasi_file');

            $table->foreign('id_kategori')
                ->references('id_kategori')
                ->on('kategori_kerjasama')
                ->nullOnDelete();

            $table->index(['id_kategori', 'is_active'], 'template_dokumen_kategori_active_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('template_dokumen', function (Blueprint $table) {
            $table->dropIndex('template_dokumen_kategori_active_idx');
            $table->dropForeign(['id_kategori']);
            $table->dropColumn(['id_kategori', 'jenis_dokumen', 'is_active']);
        });
    }
};

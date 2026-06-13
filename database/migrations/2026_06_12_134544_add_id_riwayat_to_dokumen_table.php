<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dokumen', function (Blueprint $table) {
            // ✅ Cek dulu apakah kolom sudah ada
            if (!Schema::hasColumn('dokumen', 'id_riwayat')) {
                $table->unsignedBigInteger('id_riwayat')->nullable()->after('id_kerjasama');
                
                // Foreign key (optional)
                $table->foreign('id_riwayat')
                    ->references('id_riwayat')
                    ->on('riwayat_status')
                    ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('dokumen', function (Blueprint $table) {
            if (Schema::hasColumn('dokumen', 'id_riwayat')) {
                $table->dropForeign(['id_riwayat']);
                $table->dropColumn('id_riwayat');
            }
        });
    }
};
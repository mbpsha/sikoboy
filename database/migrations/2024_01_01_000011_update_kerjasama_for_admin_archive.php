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
        Schema::table('kerjasama', function (Blueprint $table) {
            // Drop the existing non-nullable FK so we can make id_mitra nullable
            $table->dropForeign(['id_mitra']);

            // Allow null for pemerintah-type kerjasama (no registered mitra)
            $table->unsignedBigInteger('id_mitra')->nullable()->change();

            // Re-add FK with SET NULL on delete
            $table->foreign('id_mitra')
                ->references('id_mitra')
                ->on('mitras')
                ->onDelete('set null');

            // Detail fields
            $table->string('jenis_kerjasama')->nullable()->after('urusan');
            $table->string('jenis_dokumen')->nullable()->after('jenis_kerjasama');

            // Distinguish mitra-initiated vs pemerintah-initiated
            $table->enum('tipe', ['mitra', 'pemerintah'])->default('mitra')->after('jenis_dokumen');

            // Name of the external party for pemerintah type
            $table->string('nama_pihak_luar')->nullable()->after('tipe');

            // Lifecycle / status tracking
            $table->boolean('is_finalized')->default(false)->after('status_aktif');
            $table->text('status_negosiasi')->nullable()->after('is_finalized');
            $table->enum('status_persetujuan', ['disetujui', 'revisi', 'ditolak'])->nullable()->default(null)->after('status_negosiasi');
            $table->text('catatan_persetujuan')->nullable()->after('status_persetujuan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kerjasama', function (Blueprint $table) {
            $table->dropForeign(['id_mitra']);
            $table->unsignedBigInteger('id_mitra')->nullable(false)->change();
            $table->foreign('id_mitra')
                ->references('id_mitra')
                ->on('mitras')
                ->onDelete('cascade');

            $table->dropColumn([
                'jenis_kerjasama',
                'jenis_dokumen',
                'tipe',
                'nama_pihak_luar',
                'is_finalized',
                'status_negosiasi',
                'status_persetujuan',
                'catatan_persetujuan',
            ]);
        });
    }
};

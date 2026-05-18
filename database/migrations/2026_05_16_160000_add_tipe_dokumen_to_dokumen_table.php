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
        if (! Schema::hasColumn('dokumen', 'tipe_dokumen')) {
            Schema::table('dokumen', function (Blueprint $table) {
                $table->enum('tipe_dokumen', ['admin', 'mitra'])->default('admin')->after('versi_dokumen');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('dokumen', 'tipe_dokumen')) {
            Schema::table('dokumen', function (Blueprint $table) {
                $table->dropColumn('tipe_dokumen');
            });
        }
    }
};

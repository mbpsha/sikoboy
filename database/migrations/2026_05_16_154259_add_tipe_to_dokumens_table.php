<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_add_tipe_to_dokumens_table.php
    public function up(): void
    {
        if (! Schema::hasColumn('dokumen', 'tipe_dokumen')) {
            Schema::table('dokumen', function (Blueprint $table) {
                $table->enum('tipe_dokumen', ['admin', 'mitra'])->default('admin')->after('versi_dokumen');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('dokumen', 'tipe_dokumen')) {
            Schema::table('dokumen', function (Blueprint $table) {
                $table->dropColumn('tipe_dokumen');
            });
        }
    }
};

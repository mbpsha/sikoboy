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
        if (! Schema::hasColumn('users', 'status_verifikasi')) {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('status_verifikasi', ['pending', 'disetujui', 'ditolak'])
                    ->default('disetujui')
                    ->after('role');
                $table->index('status_verifikasi');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'status_verifikasi')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex(['status_verifikasi']);
                $table->dropColumn('status_verifikasi');
            });
        }
    }
};

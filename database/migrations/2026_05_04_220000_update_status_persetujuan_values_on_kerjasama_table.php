<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("UPDATE kerjasama SET status_persetujuan = 'dibatalkan' WHERE status_persetujuan = 'ditolak'");
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE kerjasama MODIFY status_persetujuan ENUM('disetujui', 'revisi', 'dibatalkan') NULL DEFAULT NULL");
        } else {
            Schema::table('kerjasama', function ($table): void {
                $table->string('status_persetujuan', 20)->nullable()->default(null)->change();
            });
        }
    }

    public function down(): void
    {
        DB::statement("UPDATE kerjasama SET status_persetujuan = 'ditolak' WHERE status_persetujuan = 'dibatalkan'");
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE kerjasama MODIFY status_persetujuan ENUM('disetujui', 'revisi', 'ditolak') NULL DEFAULT NULL");
        } else {
            Schema::table('kerjasama', function ($table): void {
                $table->string('status_persetujuan', 20)->nullable()->default(null)->change();
            });
        }
    }
};

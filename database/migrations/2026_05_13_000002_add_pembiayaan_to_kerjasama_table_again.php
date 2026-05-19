<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('kerjasama', 'pembiayaan')) {
            Schema::table('kerjasama', function (Blueprint $table) {
                $table->enum('pembiayaan', [
                    'APBN',
                    'APBD',
                    'PIHAK KETIGA',
                    'PARA PIHAK',
                    'SESUAI DENGAN PERATURAN PERUNDANG-UNDANGAN',
                ])->nullable()->after('status_aktif');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('kerjasama', 'pembiayaan')) {
            Schema::table('kerjasama', function (Blueprint $table) {
                $table->dropColumn('pembiayaan');
            });
        }
    }
};

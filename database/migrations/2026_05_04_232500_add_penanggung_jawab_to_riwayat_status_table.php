<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('riwayat_status', function (Blueprint $table): void {
            $table->string('penanggung_jawab')->nullable()->after('catatan');
        });
    }

    public function down(): void
    {
        Schema::table('riwayat_status', function (Blueprint $table): void {
            $table->dropColumn('penanggung_jawab');
        });
    }
};

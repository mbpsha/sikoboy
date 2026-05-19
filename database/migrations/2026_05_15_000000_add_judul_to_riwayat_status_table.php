<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasColumn('riwayat_status', 'judul')) {
            Schema::table('riwayat_status', function (Blueprint $table) {
                $table->string('judul')->nullable()->after('catatan');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('riwayat_status', 'judul')) {
            Schema::table('riwayat_status', function (Blueprint $table) {
                $table->dropColumn('judul');
            });
        }
    }
};

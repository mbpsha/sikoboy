<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update semua data yang error (tersimpan sebagai 0 atau 1)
        DB::table('kerjasama')->where('status_aktif', '0')->update(['status_aktif' => 'Berakhir']);
        DB::table('kerjasama')->where('status_aktif', '1')->update(['status_aktif' => 'Aktif']);

        // Pastikan NULL berubah ke 'Aktif' (default)
        DB::table('kerjasama')->whereNull('status_aktif')->update(['status_aktif' => 'Aktif']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu rollback
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tambahkan status "Dibatalkan" agar tersedia saat admin mengubah
        // status kerjasama di riwayat (RiwayatKerjasamaController::updateStatus).
        DB::table('status')->updateOrInsert(
            ['jenis_status' => 'Dibatalkan'],
            ['jenis_status' => 'Dibatalkan']
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('status')->where('jenis_status', 'Dibatalkan')->delete();
    }
};

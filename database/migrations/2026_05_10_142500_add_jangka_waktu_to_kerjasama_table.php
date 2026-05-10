<?php

use App\Support\KerjasamaDuration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kerjasama', function (Blueprint $table) {
            $table->unsignedInteger('jangka_waktu')->nullable()->after('status_aktif');
        });

        DB::table('kerjasama')
            ->orderBy('id_kerjasama')
            ->chunkById(100, function ($rows): void {
                foreach ($rows as $row) {
                    $latestPeriode = DB::table('periode_kerjasama')
                        ->where('id_kerjasama', $row->id_kerjasama)
                        ->orderByDesc('tanggal_berakhir')
                        ->orderByDesc('id_periode')
                        ->first(['tanggal_mulai', 'tanggal_berakhir']);

                    if ($latestPeriode === null) {
                        continue;
                    }

                    $jangkaWaktu = KerjasamaDuration::months(
                        $latestPeriode->tanggal_mulai,
                        $latestPeriode->tanggal_berakhir
                    );

                    DB::table('kerjasama')
                        ->where('id_kerjasama', $row->id_kerjasama)
                        ->update(['jangka_waktu' => $jangkaWaktu]);
                }
            }, 'id_kerjasama', 'id_kerjasama');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kerjasama', function (Blueprint $table) {
            $table->dropColumn('jangka_waktu');
        });
    }
};

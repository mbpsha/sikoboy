<?php

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
            if (! Schema::hasColumn('kerjasama', 'pemrakarsa')) {
                $table->enum('pemrakarsa', ['M', 'P'])->default('M')->after('nama_pihak_luar');
            }

            if (! Schema::hasColumn('kerjasama', 'nomor_suratM')) {
                $table->string('nomor_suratM')->nullable()->after('judul');
            }

            if (! Schema::hasColumn('kerjasama', 'nomor_suratP')) {
                $table->string('nomor_suratP')->nullable()->after('nomor_suratM');
            }
        });

        if (Schema::hasColumn('kerjasama', 'tipe')) {
            DB::table('kerjasama')->update([
                'pemrakarsa' => DB::raw("CASE WHEN tipe = 'pemerintah' THEN 'P' ELSE 'M' END"),
            ]);
        }

        if (Schema::hasColumn('kerjasama', 'nomor_surat')) {
            DB::table('kerjasama')
                ->whereNotNull('nomor_surat')
                ->update([
                    'nomor_suratM' => DB::raw("CASE WHEN pemrakarsa = 'M' THEN nomor_surat ELSE nomor_suratM END"),
                    'nomor_suratP' => DB::raw("CASE WHEN pemrakarsa = 'P' THEN nomor_surat ELSE nomor_suratP END"),
                ]);
        }

        Schema::table('kerjasama', function (Blueprint $table) {
            if (Schema::hasColumn('kerjasama', 'nomor_surat')) {
                $table->dropColumn('nomor_surat');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kerjasama', function (Blueprint $table) {
            if (! Schema::hasColumn('kerjasama', 'nomor_surat')) {
                $table->string('nomor_surat')->nullable()->after('judul');
            }
        });

        DB::table('kerjasama')->update([
            'nomor_surat' => DB::raw('COALESCE(nomor_suratM, nomor_suratP)'),
        ]);

        Schema::table('kerjasama', function (Blueprint $table) {
            if (Schema::hasColumn('kerjasama', 'nomor_suratM')) {
                $table->dropColumn('nomor_suratM');
            }

            if (Schema::hasColumn('kerjasama', 'nomor_suratP')) {
                $table->dropColumn('nomor_suratP');
            }

            if (Schema::hasColumn('kerjasama', 'pemrakarsa')) {
                $table->dropColumn('pemrakarsa');
            }
        });
    }
};

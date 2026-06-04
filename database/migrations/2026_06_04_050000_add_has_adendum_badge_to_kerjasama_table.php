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
            if (! Schema::hasColumn('kerjasama', 'has_adendum_badge')) {
                $table->boolean('has_adendum_badge')->default(false)->after('status_persetujuan');
            }
        });

        if (Schema::hasColumn('kerjasama', 'has_adendum_badge')) {
            DB::table('kerjasama')
                ->whereIn('id_kerjasama', function ($query) {
                    $query->select('id_kerjasama')
                        ->from('adendum')
                        ->distinct();
                })
                ->update(['has_adendum_badge' => true]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kerjasama', function (Blueprint $table) {
            if (Schema::hasColumn('kerjasama', 'has_adendum_badge')) {
                $table->dropColumn('has_adendum_badge');
            }
        });
    }
};

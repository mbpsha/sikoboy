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
        Schema::table('template_dokumen', function (Blueprint $table) {
            if (! Schema::hasColumn('template_dokumen', 'judul')) {
                $table->string('judul')->nullable()->after('id_kategori');
            }

            if (! Schema::hasColumn('template_dokumen', 'deskripsi')) {
                $table->text('deskripsi')->nullable()->after('judul');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('template_dokumen', function (Blueprint $table) {
            if (Schema::hasColumn('template_dokumen', 'deskripsi')) {
                $table->dropColumn('deskripsi');
            }

            if (Schema::hasColumn('template_dokumen', 'judul')) {
                $table->dropColumn('judul');
            }
        });
    }
};

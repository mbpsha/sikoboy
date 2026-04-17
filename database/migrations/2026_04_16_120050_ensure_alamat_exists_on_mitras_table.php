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
        if (! Schema::hasColumn('mitras', 'alamat')) {
            Schema::table('mitras', function (Blueprint $table) {
                $table->text('alamat')->nullable()->after('pic');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // no-op: avoid dropping existing alamat column
    }
};

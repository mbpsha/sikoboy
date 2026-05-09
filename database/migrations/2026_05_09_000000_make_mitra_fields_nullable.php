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
        Schema::table('mitras', function (Blueprint $table) {
            // Make id_user nullable and drop unique constraint
            $table->dropForeign(['id_user']);
            $table->unsignedBigInteger('id_user')->nullable()->change();
            
            // Make other fields nullable
            $table->string('no_handphone')->nullable()->change();
            $table->string('pic')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mitras', function (Blueprint $table) {
            $table->unsignedBigInteger('id_user')->nullable(false)->change();
            $table->string('no_handphone')->nullable(false)->change();
            $table->string('pic')->nullable(false)->change();
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
        });
    }
};

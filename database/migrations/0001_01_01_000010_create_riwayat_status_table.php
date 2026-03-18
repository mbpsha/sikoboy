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
        Schema::create('riwayat_status', function (Blueprint $table) {
            $table->id('id_riwayat');
            $table->unsignedBigInteger('id_kerjasama');
            $table->unsignedBigInteger('id_status');
            $table->unsignedBigInteger('id_admin');
            $table->string('catatan');
            $table->timestamp('tanggal');

            $table->foreign('id_kerjasama')->references('id_kerjasama')->on('kerjasama')->onDelete('cascade');
            $table->foreign('id_status')->references('id_status')->on('status')->onDelete('restrict');
            $table->foreign('id_admin')->references('id_admin')->on('admins')->onDelete('restrict');

            $table->index(['id_kerjasama', 'id_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_status');
    }
};

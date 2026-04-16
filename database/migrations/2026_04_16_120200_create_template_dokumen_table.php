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
        Schema::create('template_dokumen', function (Blueprint $table) {
            $table->id('id_template_dokumen');
            $table->unsignedBigInteger('id_admin');
            $table->string('nama_file');
            $table->string('lokasi_file');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('id_admin')->references('id_admin')->on('admins')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_dokumen');
    }
};

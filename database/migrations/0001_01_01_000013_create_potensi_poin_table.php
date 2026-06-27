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
        Schema::create('potensi_poin', function (Blueprint $table) {
            $table->id('id_potensi_poin');
            $table->unsignedBigInteger('id_potensi');
            $table->text('isi');
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('id_potensi')->references('id_potensi')->on('potensi')->onDelete('cascade');
            $table->index(['id_potensi', 'urutan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('potensi_poin');
    }
};

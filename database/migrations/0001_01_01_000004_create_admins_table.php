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
        Schema::create('admins', function (Blueprint $table) {
            $table->unsignedBigInteger('id_user')->primary();
            $table->string('nip', 50)->unique()->nullable();
            $table->string('nama_lengkap');
            $table->string('jabatan', 100)->nullable();
            $table->string('bagian', 100)->nullable();
            $table->string('no_handphone', 15)->nullable();
            $table->string('foto_profil')->nullable();
            $table->timestamps();

            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};

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
        Schema::create('mitras', function (Blueprint $table) {
            $table->unsignedBigInteger('id_user')->primary();
            $table->string('nama_perusahaan');
            $table->string('npwp', 20)->unique()->nullable();
            $table->string('pic', 100);
            $table->string('jabatan_pic', 100)->nullable();
            $table->string('no_handphone', 15);
            $table->string('no_telepon', 15)->nullable();
            $table->text('alamat');
            $table->string('provinsi', 100)->nullable();
            $table->string('kabupaten_kota', 100)->nullable();
            $table->string('kecamatan', 100)->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->string('bidang_usaha', 100)->nullable();
            $table->string('website')->nullable();
            $table->string('logo_perusahaan')->nullable();
            $table->timestamps();

            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mitras');
    }
};

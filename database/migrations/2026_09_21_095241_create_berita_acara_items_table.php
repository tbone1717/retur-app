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
        Schema::create('berita_acara', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_bap');
            $table->date('tanggal');
            $table->string('jam')->nullable();
            $table->string('depo');
            $table->string('region')->default('JABOTABEK');
            $table->string('tempat')->nullable();
            $table->string('cara_pemusnahan');
            $table->string('nama_pembuat')->nullable();
            $table->string('jabatan_pembuat')->nullable();
            $table->string('saksi_1_nama')->nullable();
            $table->string('saksi_1_jabatan')->nullable();
            $table->string('saksi_2_nama')->nullable();
            $table->string('saksi_2_jabatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('berita_acara_items');
    }
};
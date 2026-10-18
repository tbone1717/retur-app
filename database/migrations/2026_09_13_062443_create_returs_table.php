<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('returs', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_bon_in');
            $table->string('nama_toko');
            $table->string('driver');
            $table->string('b2b')->nullable();
            $table->string('nrb_outlet')->nullable();
            $table->date('tanggal_bon')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('returs');
    }
};
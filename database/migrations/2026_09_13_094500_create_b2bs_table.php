<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('b2bs', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_bon_in');
            $table->string('nama_toko');
            $table->string('nrb_outlet')->nullable();
            $table->string('outlet');
            $table->string('status')->default('pending');
            $table->date('tanggal_bon')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('b2bs');
    }
};
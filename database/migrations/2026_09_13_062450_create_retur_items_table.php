<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('retur_items', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel returs, jika header dihapus maka item ikut terhapus (cascade)
            $table->foreignId('retur_id')->constrained('returs')->onDelete('cascade');
            $table->string('sku');
            $table->integer('qty');
            $table->string('status')->nullable();
            $table->string('barcode')->nullable();
            $table->decimal('sum_price', 12, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('retur_items');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('b2b_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('b2b_id')->constrained('b2bs')->onDelete('cascade');
            $table->string('sku');
            $table->integer('qty');
            $table->string('status')->nullable();
            $table->string('warning')->nullable();
            $table->string('barcode')->nullable();
            $table->decimal('sum_price', 15, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('b2b_items');
    }
};
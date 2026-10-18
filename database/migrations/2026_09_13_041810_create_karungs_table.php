<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
        {
            Schema::create('karungs', function (Blueprint $table) {
                $table->id();
                $table->string('no_karung')->unique();
                $table->date('tanggal');
                $table->string('jenis_produk');
                $table->string('status');
                $table->timestamps();
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karungs');
    }
};

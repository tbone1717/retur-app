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
        Schema::create('pemusnahans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karung_id')->constrained('karungs')->onDelete('cascade');
            $table->integer('tahap'); // Tahap 1 sampai 4
            $table->date('tanggal_pengajuan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemusnahans');
    }
};

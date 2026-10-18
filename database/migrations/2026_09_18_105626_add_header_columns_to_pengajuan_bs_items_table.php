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
        Schema::table('pengajuan_bs_items', function (Blueprint $table) {
            $table->string('nomor_pengajuan')->nullable()->after('id');
            $table->date('tanggal_pengajuan')->nullable()->after('nomor_pengajuan');
            $table->string('depo_asal')->nullable()->after('periode');
            $table->string('jenis_pemusnahan')->nullable()->after('depo_asal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_bs_items', function (Blueprint $table) {
            $table->dropColumn([
                'nomor_pengajuan', 
                'tanggal_pengajuan', 
                'depo_asal', 
                'jenis_pemusnahan'
            ]);
        });
    }
};
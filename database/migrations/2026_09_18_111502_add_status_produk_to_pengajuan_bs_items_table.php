<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan_bs_items', function (Blueprint $table) {
            $table->string('status_produk')->nullable()->after('jenis_pemusnahan');
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_bs_items', function (Blueprint $table) {
            $table->dropColumn('status_produk');
        });
    }
};
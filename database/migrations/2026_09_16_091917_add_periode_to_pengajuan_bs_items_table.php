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
            // Menambahkan kolom periode bertipe string setelah kolom id
            $table->string('periode')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_bs_items', function (Blueprint $table) {
            // Menghapus kolom jika migration di-rollback
            $table->dropColumn('periode');
        });
    }
};
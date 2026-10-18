<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mutasi_gs_bs_headers', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_mutasi')->unique();
            $table->date('tanggal');
            $table->string('depo');
            $table->string('region')->default('JABOTABEK');
            $table->string('gudang_asal')->default('Gudang Bagus (GS)');
            $table->string('type_stock_asal')->default('GS');
            $table->string('gudang_tujuan')->default('Bad Stock (BS)');
            $table->string('minggu_ke'); // Contoh: Week 1, Week 2, dst
            $table->string('keputusan_transaksi')->default('Mutasi Internal Depo');
            $table->timestamps();
        });

        Schema::create('mutasi_gs_bs_items', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_mutasi');
            $table->string('kode_produk');
            $table->integer('qty_ed')->default(0);
            $table->integer('qty_hm')->default(0);
            $table->integer('qty_kr')->default(0);
            $table->integer('qty_ns')->default(0);
            $table->integer('qty_ll')->default(0);
            $table->integer('qty_ned')->default(0);
            $table->integer('total_pcs')->default(0);
            $table->decimal('jumlah_harga', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mutasi_gs_bs_items');
        Schema::dropIfExists('mutasi_gs_bs_headers');
    }
};
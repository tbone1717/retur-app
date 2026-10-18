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
        Schema::create('pengajuan_bs_items', function (Blueprint $table) {
            $table->id();
            
            // Opsional: Jika Anda punya tabel Header Pengajuan (untuk Region, Depo, Tanggal)
            // $table->foreignId('pengajuan_id')->constrained('pengajuan_bs')->cascadeOnDelete();
            
            // Kode Produk (Merujuk ke Master Produk)
            $table->string('kode_produk');
            
            // --- Kolom Alasan Pemusnahan (In PCS) ---
            $table->integer('qty_ed')->default(0);  // Expired Date
            $table->integer('qty_hm')->default(0);  // Hama
            $table->integer('qty_kr')->default(0);  // Kemasan Rusak
            $table->integer('qty_ns')->default(0);  // Non-Standar
            $table->integer('qty_ned')->default(0); // Near Expired Date
            
            // --- Kolom Total Pengajuan ---
            // Sebaiknya total ini tetap disimpan di database agar mempercepat query saat export Excel
            $table->integer('total_pcs')->default(0);
            $table->decimal('total_uom', 10, 2)->default(0); // Desimal karena uom Dus/Bal bisa berkoma (misal 13.15)
            $table->decimal('jumlah_harga', 15, 2)->default(0); // Rupiah
            
            // --- Kolom Keterangan Tambahan ---
            $table->date('tanggal_ed')->nullable();
            $table->text('keterangan_proling')->nullable(); // Menggunakan text karena keterangannya bisa panjang
            $table->string('kode_ed')->nullable(); // Untuk kolom "Approved Extraordinary / Validasi EO"
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_bs_items');
    }
};
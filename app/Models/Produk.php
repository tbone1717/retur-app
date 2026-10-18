<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;
    
    protected $table = 'produks'; // Menggunakan tabel 'produks'

    protected $fillable = [
        'sku',
        'keterangan',
        'uom',
        'harga',
        'status',
        'barcode'
    ];
}
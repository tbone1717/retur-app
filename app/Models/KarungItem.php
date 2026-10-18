<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class KarungItem extends Model
{
    protected $guarded = [];

    public function karung()
    {
        return $this->belongsTo(Karung::class);
    }

    public function produk() 
    { 
        return $this->belongsTo(Produk::class, 'sku', 'sku'); 
    }
}
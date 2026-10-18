<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Karung extends Model
{
    protected $guarded = [];

    public function items()
    {
        return $this->hasMany(KarungItem::class);
    }

    // Tambahkan relasi ini untuk mengecek status pengajuan pemusnahan
    public function pemusnahan()
    {
        return $this->hasOne(Pemusnahan::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function retur()
    {
        return $this->belongsTo(Retur::class, 'retur_id');
    }
}
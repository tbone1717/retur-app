<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pemusnahan extends Model
{
    protected $guarded = [];

    public function karung()
    {
        return $this->belongsTo(Karung::class);
    }
}
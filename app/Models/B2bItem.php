<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class B2bItem extends Model
{
    use HasFactory;

    protected $table = 'b2b_items';
    protected $guarded = ['id'];

    public function b2b()
    {
        return $this->belongsTo(B2b::class, 'b2b_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class B2b extends Model
{
    use HasFactory;

    protected $table = 'b2bs';
    protected $guarded = ['id'];

    public function items()
    {
        return $this->hasMany(B2bItem::class, 'b2b_id');
    }
}
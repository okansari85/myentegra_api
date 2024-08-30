<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelProductsPazaramaProducts extends Model
{
    use HasFactory;

    protected $table = 'rel_product_pazarama_product';

    protected $fillable = [
        'pazarama_product_id',
        'product_id',
    ];

    public function pazarama_product(){
        return $this->belongsTo(PazaramaProduct::class,'pazarama_product_id');
    }

    public function product()
    {
        return $this->belongsTo(Products::class,'product_id');
    }
}

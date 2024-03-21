<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelProductsN11Products extends Model
{
    use HasFactory;

    protected $table = 'rel_product_n11_product';

    protected $fillable = [
        'n11_id',
        'product_id',
    ];

    public function n11_product(){
        return $this->belongsTo(N11Products::class,'n11_id');
    }

    public function product()
    {
        return $this->belongsTo(Products::class,'product_id');
    }

}

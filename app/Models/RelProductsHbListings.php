<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelProductsHbListings extends Model
{
    use HasFactory;

    protected $table = 'rel_products_hblistings';

    protected $fillable = [
        'product_id',
        'hb_listing_id',
    ];

    public function hb_listing(){
        return $this->belongsTo(HBListings::class,'hb_listing_id');
    }

    public function product()
    {
        return $this->belongsTo(Products::class,'product_id');
    }

}

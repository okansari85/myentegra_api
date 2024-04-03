<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImages extends Model
{
    use HasFactory;

    protected $table = 'product_images';

    protected $fillable = ['name','type','order','url','product_id'];


    public function product(): BelongsTo
    {
        return $this->belongsTo(Products::class, 'id');
    }


}

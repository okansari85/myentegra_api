<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hakedis extends Model
{
    use HasFactory;

    protected $table = 'hakedis';
    protected $guarded = ['id'];

    protected $casts = [
        'price' => 'decimal:2',
        'packet_price' => 'decimal:2',
        'total_price' => 'decimal:2'
    ];

    public function product()
    {
        return $this->belongsTo(Products::class,'product_id');
    }

    // Hakedis'in bir Order'a ait olduğunu belirtir
    public function order()
    {
        return $this->belongsTo(Orders::class,'order_id');
    }
}

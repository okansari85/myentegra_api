<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItems extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function order()
    {
        return $this->belongsTo(Orders::class,'id');
    }

    public function orderable()
    {
        return $this->morphTo();
    }

    public function product(){
        return $this->belongsTo(Products::class,'product_id');
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    use HasFactory;

    protected $table = 'orders';
    protected $guarded = ['id'];

    public function buyer(){
        return  $this->belongsTo(Buyers::class,'buyer_id');
    }

    public function buyerable()
    {
        return $this->morphTo();
    }

    public function items()
    {
        return $this->hasMany(OrderItems::class,'order_id');
    }


}

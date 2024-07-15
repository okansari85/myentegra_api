<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buyers extends Model
{
    use HasFactory;

    protected $table = 'buyers';

    protected $guarded = ['id'];

    public function adresses()
    {
        return $this->hasMany(BuyerAdress::class, 'buyer_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Orders::class, 'buyer_id');
    }

    public function n11_order()
    {
        return $this->morphOne(Orders::class, 'buyerable');
    }



}


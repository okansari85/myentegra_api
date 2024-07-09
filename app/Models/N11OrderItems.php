<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class N11OrderItems extends Model
{
    use HasFactory;

    protected $table = 'n11_order_items';
    protected $guarded = ['id'];

    public function orders (){
        return $this->belongsTo(Orders::class, 'order_id');
    }

}

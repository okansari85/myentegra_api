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

    public function items(){

        $order_item_class = OrderItems::class;

        switch ($this->platformId) {
            case 1:
                $order_item_class = N11OrderItems::class;
                break;
            default:
                $order_item_class = OrderItems::class;
                break;
        }

       // Diğer durumlarda varsayılan ilişkiyi döndür (bu kısmı kendi mantığınıza göre ayarlayabilirsiniz)
        return $this->hasMany($order_item_class, 'order_id');

    }

}

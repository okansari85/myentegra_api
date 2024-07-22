<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HbOrderItems extends Model
{
    use HasFactory;

    protected $table = 'hb_order_items'; // Veritabanı tablosu adı

    protected $fillable = [
        'order_id',
        'hb_listing_id',
        'productName',
        'orderNumber',
        'orderDate',
        'listing_id',
        'lineItemId',
        'merchantId',
        'hbSku',
        'merchantSku',
        'quantity',
        'price',
        'vat',
        'totalPrice',
        'commission',
        'commissionRate',
        'unitHBDiscount',
        'totalHBDiscount',
        'unitMerchantDiscount',
        'totalMerchantDiscount',
        'merchantUnitPrice',
        'merchantTotalPrice',
        'cargoPaymentInfo',
        'deliveryType',
        'vatRate',
        'warehouse',
        'productBarcode',
    ];


    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(HBListings::class,'hb_listing_id');
    }

    public function orderItem()
    {
        return $this->morphOne(OrderItems::class, 'orderable');
    }


}

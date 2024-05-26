<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'orderDate',
        'shipmentId',
        'platformId',
        'isPaymentMade',
        'market_order_id',
        'market_order_number',
        'is_confirmed',
        'is_invoice_issued',
        'status',
        'invoiceType',
        'paymentType',
        'buyer_id'
    ];

}

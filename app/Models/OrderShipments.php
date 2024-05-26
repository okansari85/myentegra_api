<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderShipments extends Model
{
    use HasFactory;

    protected $table = 'order_shipments';

    protected $fillable = [
        'order_id',
        'trackingNumber',
        'shipmentCompanyName',
        'shipmentCompanyShortName',
        'shipmentCode',
        'shipmentMethod',
        'campaignNumberStatus',
        'shippedDate',
        'campaginNumber',
    ];

}

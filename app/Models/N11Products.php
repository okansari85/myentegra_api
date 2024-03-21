<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class N11Products extends Model
{
    use HasFactory;

    protected $table = 'n11_products';

    protected $fillable = [
        'n11_id',
        'title',
        'display_price',
        'price',
        'productSellerCode',
        'description',
        'n11_category_id',
        'stock_item_n11_catalog_id',
        'stock_item_quantity',
        'shipmentTemplate',
        'approvalStatus',
        'saleStatus',
        'preparingDay',
        'productCondition',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'display_price' => 'decimal:2'
    ];

    public function product(){
        return  $this->hasOne(RelProductsN11Products::class,"n11_id");
    }



}

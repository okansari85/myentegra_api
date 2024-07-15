<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HBListings extends Model
{
    use HasFactory;

    protected $table = 'hb_listings';
    protected $primaryKey = 'id';


    protected $fillable = [
        'listing_id',
        'unique_identifier',
        'hepsiburada_sku',
        'merchant_sku',
        'price',
        'available_stock',
        'dispatch_time',
        'cargo_company1',
        'cargo_company2',
        'cargo_company3',
        'shipping_address_label',
        'shipping_profile_name',
        'claim_address_label',
        'maximum_purchasable_quantity',
        'minimum_purchasable_quantity',
        'final_price',
        'pricing_start_date',
        'pricing_end_date',
        'debtor_name',
        'debtor_amount',
        'is_salable',
        'customizable_properties',
        'deactivation_reasons',
        'is_suspended',
        'is_locked',
        'lock_reasons',
        'is_frozen',
        'freeze_reasons',
        'commission_rate',
        'available_warehouses',
        'is_fulfilled_by_hb',
        'price_increase_disabled',
        'price_decrease_disabled',
        'stock_decrease_disabled'
    ];

    protected $casts = [
        'customizable_properties' => 'array',
        'deactivation_reasons' => 'array',
        'lock_reasons' => 'array',
        'freeze_reasons' => 'array',
        'available_warehouses' => 'array',
        'pricing_start_date' => 'datetime',
        'pricing_end_date' => 'datetime'
    ];
}

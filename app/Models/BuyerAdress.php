<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuyerAdress extends Model
{
    use HasFactory;

    protected $table = 'buyer_adress';

    protected $fillable = [
        'created_at',
        'updated_at',
        'adressType',
        'fullName',
        'city',
        'district',
        'neighborhood',
        'postalCode',
        'gsm',
        'tcId',
        'taxId',
        'taxHouse',
        'buyer_id'
    ];
}

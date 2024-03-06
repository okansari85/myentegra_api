<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class N11CargoPrices extends Model
{
    use HasFactory;

    protected $table = 'n11_cargo_prices';

    protected $fillable = [
        'desi',
        'yk_price',
    ];

}

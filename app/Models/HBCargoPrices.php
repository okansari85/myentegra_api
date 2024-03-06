<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HBCargoPrices extends Model
{
    use HasFactory;

    protected $table = 'hb_cargo_prices';

    protected $fillable = [
        'desi',
        'aras_price',
        'mng_price',
        'yk_price',
        'surat_price',
        'ptt_price',
        'created_at',
        'updated_at'
    ];
}

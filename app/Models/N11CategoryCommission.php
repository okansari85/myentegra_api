<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class N11CategoryCommission extends Model
{
    use HasFactory;

    protected $table = 'n11_category_commision';

    protected $fillable = [
        'cat4',
        'cat3',
        'cat2',
        'cat1',
        'komsiyon_orani',
        'pazarlama_hizmet_orani',
        'pazaryeri_hizmet_orani',
        'n11_category_id',
    ];
}

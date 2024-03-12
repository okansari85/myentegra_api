<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class N11CategoryCommission extends Model
{
    use HasFactory;

    protected $table = 'n11_category_commision';

    protected $fillable = [
        'category_name',
        'komsiyon_orani',
        'pazarlama_hizmet_orani',
        'pazaryeri_hizmet_orani',
        'n11_category_id',
    ];

    
}

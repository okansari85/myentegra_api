<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PazaramaProductImage extends Model
{
    use HasFactory;

    protected $table = 'pazarama_product_images';

    protected $fillable = [
        'pazarama_product_id',
        'imageUrl',
    ];

    public function product()
    {
        return $this->belongsTo(PazaramaProduct::class, 'pazarama_product_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PazaramaProduct extends Model
{
    use HasFactory;


    protected $table = 'pazarama_products';

    protected $fillable = [
        'name',
        'displayName',
        'description',
        'brandId',
        'brandName',
        'code',
        'stockCount',
        'stockCode',
        'priorityRank',
        'vatRate',
        'listPrice',
        'salePrice',
        'installmentCount',
        'categoryId',
        'state',
        'stateDescription',
        'isCatalogProduct',
        'groupCode',
    ];

    // İlişki tanımları
    public function attributes()
    {
        return $this->hasMany(PazaramaProductAttribute::class, 'pazarama_product_id'); // İlişki anahtarı
    }

    public function images()
    {
        return $this->hasMany(PazaramaProductImage::class, 'pazarama_product_id'); // İlişki anahtarı
    }

    public function product(){
        return  $this->hasOne(RelProductsPazaramaProducts::class,"pazarama_product_id");
    }


}

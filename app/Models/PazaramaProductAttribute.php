<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PazaramaProductAttribute extends Model
{
    use HasFactory;

    protected $table = 'pazarama_product_attributes'; // Tablonuzun adı

    protected $fillable = [
        'pazarama_product_id', // Güncellenmiş isim
        'attributeId',
        'attributeValueId',
    ];

    // İlişki tanımı
    public function product()
    {
        return $this->belongsTo(PazaramaProduct::class, 'pazarama_product_id'); // İlişki anahtarı
    }
}

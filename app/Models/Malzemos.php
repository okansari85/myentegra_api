<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Malzemos extends Model
{
    use HasFactory;

    protected $table = 'malzemos';


    protected $fillable = [
        'raf_id',
        'depo_id',
        'productCode',
        'productDesc',
        'stock',
    ];

    public function raf()
    {
        return $this->belongsTo(Depos::class);
    }

    public function depo()
    {
        return $this->belongsTo(Depos::class);
    }

    public function stokhareketleri(){
        return $this->hasMany(StokHareketleri::class, 'product_id');
    }
}

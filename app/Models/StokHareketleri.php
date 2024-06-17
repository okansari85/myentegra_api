<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokHareketleri extends Model
{
    use HasFactory;


    protected $table = 'stok_hareketleri';

    protected $fillable = ['product_id', 'stock','type'];

    public function malzemos()
    {
        return $this->belongsTo(Malzemos::class,'product_id','id');
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'productCode',
        'price',
        'desi',
        'stock',
        'category_id'
    ];

    protected $casts = [
        'price' => 'decimal:2'
    ];


    public function images()
    {
        return $this->hasMany(ProductImages::class, 'product_id');
    }

    public function coverImage()
    {
        return $this->hasOne(ProductImages::class,'product_id')
            ->ofMany('cover', 'max')
            ->withDefault();
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function n11_product(){
        return  $this->hasOne(RelProductsN11Products::class,"product_id");
    }

    public function next(){
        // get next user
        return $this->where('id', '>', $this->id)->orderBy('id','asc')->first();

    }
    public  function previous(){
        // get previous  user
        return $this->where('id', '<', $this->id)->orderBy('id','desc')->first();

    }

    public function first(){
        return $this->first();
    }

    public function maliyet(){
        $price =  $this->price * 1.1 * 1.2;
        $price = $price + 30 ;
        $price = $price * $this->profit_rate;
        return number_format((float)$price, 2, '.', '');
    }


}

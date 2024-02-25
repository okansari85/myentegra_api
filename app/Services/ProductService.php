<?php

namespace App\Services;

use App\Interfaces\IProducts;
use App\Models\Products;
use Illuminate\Support\Facades\DB;

class ProductService implements IProducts
{
    public function getAllProducts($search,$per_page){

        return response()->json(Products::select('id', 'productCode', 'productTitle','price', 'desi', 'created_at', 'updated_at')
        ->where(function ($query) use ($search) {
              $query->where(DB::raw('lower(productCode)'), 'like', '%' . mb_strtolower($search) . '%');
         })->orderBy('id','desc')
           ->paginate(5)
           ->appends(request()->query()),200);

    }
}

<?php

namespace App\Services;

use App\Interfaces\IProducts;
use App\Models\Products;
use Illuminate\Support\Facades\DB;

class ProductService implements IProducts
{
    public function getAllProducts($search,$per_page){

        return response()->json(Products::with('coverImage','category.descendants')
        ->select('id', 'category_id','productCode','stock', 'productTitle','profit_rate','price', 'desi', 'created_at', 'updated_at')
        ->where(function ($query) use ($search) {
              $query->where(DB::raw('lower(productCode)'), 'like', '%' . mb_strtolower($search) . '%');
         })->orderBy('id','desc')
           ->paginate($per_page)
           ->appends(request()->query()),200);

    }

    public function addProductCoverImage($file,$product_id){

        $product = Products::find($product_id);

        $product
        ->images()
        ->create([
            'file' => $request->file('cover')->store('images'),
            'cover' => true,
        ]);

    }
}

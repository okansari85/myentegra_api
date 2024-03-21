<?php

namespace App\Services;

use App\Interfaces\IProducts;
use App\Models\Products;
use App\Models\N11Products;
use App\Models\RelProductsN11Products;
use Illuminate\Support\Facades\DB;

class ProductService implements IProducts
{
    public function getAllProducts($search,$per_page){

        return response()->json(Products::with('coverImage','category.descendants','n11_product.n11_product')
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

    public function matchN11Product($n11_product, $db_product){

        $n11_product = N11Products::updateOrCreate([
            'n11_id' =>  $n11_product['id']
            ],[
            'title' => $n11_product['title'],
            'display_price' => $n11_product['displayPrice'],
            'price' => $n11_product['price'],
            'productSellerCode' => $n11_product['stockItems']['stockItem']['sellerStockCode'],
            'description' => $n11_product['description'],
            'n11_category_id' => $n11_product['category']['id'],
            'stock_item_n11_catalog_id' => $n11_product['stockItems']['stockItem']['n11CatalogId'],
            'stock_item_quantity' => $n11_product['stockItems']['stockItem']['quantity'],
            'shipmentTemplate' => $n11_product['shipmentTemplate'],
            'approvalStatus' => $n11_product['approvalStatus'],
            'saleStatus' => $n11_product['saleStatus'],
            'preparingDay' => $n11_product['preparingDay'],
            'productCondition' => $n11_product['productCondition']
        ]);

        //RelProductN11Product tablosuna kayıtları ekle ya da update et
        //sadece product id kontrolü yapılacak
        //eğer başka bir ürün ile eşleşmedi kontorlü yapılacak


        $relpn11 = RelProductsN11Products::updateOrCreate(
            ['product_id'=>$db_product['id']],
            ['n11_id'=>$n11_product['id']]);

        $product= Products::with('coverImage','category.descendants','n11_product.n11_product')->get()->find($db_product['id']);
        return response()->json($product,200);

    }
}

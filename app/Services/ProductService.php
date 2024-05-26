<?php

namespace App\Services;

use App\Interfaces\IProducts;

use App\Models\Products;
use App\Models\N11Products;
use App\Models\RelProductsN11Products;
use App\Models\ProductImages;


use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Bus;
use App\Jobs\SendProductsPriceToN11;


class ProductService implements IProducts
{
    public function getAllProducts($search,$per_page){

        return response()->json(Products::with('coverImage','category.descendants','n11_product.n11_product','images')
        ->select('id','description', 'category_id','productCode','stock', 'productTitle','profit_rate','price', 'desi', 'created_at', 'updated_at')
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

        $n11_product = N11Products::updateOrCreate(
            [
            'n11_id' =>  $n11_product['id']
            ],
            [
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

        $relpn11 = RelProductsN11Products::updateOrCreate(
            ['product_id'=>$db_product['id']],
            ['n11_id'=>$n11_product['id']]);

        $product= Products::with('coverImage','category.descendants','n11_product.n11_product')->get()->find($db_product['id']);

        //add job
        $this->addJobUpdateOneProductQuantityAndPrice($product);

        return response()->json($product,200);

    }

    public function addJobUpdateOneProductQuantityAndPrice($product){
        $batch = Bus::batch([])->name('n11pricestockupdate')->dispatch();
        $batch->add(new SendProductsPriceToN11($product));
        return $batch;
    }

    public function addProduct($obj){


        $product = Products::create([
            'productTitle' => $obj['productTitle'],
            'productCode' =>  $obj['productCode'],
            'category_id' => $obj['category_id'],
            'desi' => $obj['desi'],
            'stock' => $obj['stock'],
            'price' => $obj['price'],
            'profit_rate' =>  $obj['profitRate'],
            'description' =>  $obj['description'],
        ]);

        foreach ($obj['productImages'] as $image) {
            // Her bir resmin id'sini al
            $imageId = $image['file']['id'];

            // Güncelleme yap
            ProductImages::where('id', $imageId)
                ->update(['product_id' => $product->id]);
        }

        return response()->json(['message' => 'Product and images created successfully'], 201);



    }
}

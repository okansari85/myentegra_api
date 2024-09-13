<?php

namespace App\Services;

use App\Interfaces\IProducts;

use App\Models\Products;
use App\Models\N11Products;
use App\Models\RelProductsN11Products;
use App\Models\ProductImages;
use App\Models\HBListings;
use App\Models\RelProductsHbListings;


use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Bus;
use App\Jobs\SendProductsPriceToN11;
use App\Jobs\SendProductsStocksToN11;


class ProductService implements IProducts
{
    public function getAllProducts($search,$per_page){

        return response()->json(Products::with('coverImage','category.descendants','n11_product.n11_product','hb_product.hb_listing','images')
        ->select('id','description', 'category_id','productCode','stock', 'productTitle','profit_rate','price', 'desi', 'created_at', 'updated_at','supplier_id')
        ->where(function ($query) use ($search) {
              $query->where(DB::raw('lower(productCode)'), 'like', '%' . mb_strtolower($search) . '%');
         })->orderBy('id','desc')
           ->paginate($per_page)
           ->appends(request()->query())
           ->through(function ($product) {
            // Maliyet hesaplamasını modelde tanımlı methoda göre yap
            $product->cost = $product->calculateCost(); // Örneğin calculateCost() adında bir metot
            $product->profit = $product->calculateProfit();
            $product->last = $product->lastPrice();

            return $product;
            })
           ,200);

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

        $product= Products::with('coverImage','category.descendants','n11_product.n11_product','hb_product.hb_listing')->get()->find($db_product['id']);

        //add job
        $this->addJobUpdateOneProductQuantityAndPrice($product);

        return response()->json($product,200);

    }

    public function addJobUpdateOneProductQuantityAndPrice($product){
        $batch = Bus::batch([])->name('n11pricestockupdate')->dispatch();

        $arr=[
             new SendProductsPriceToN11($product),
             new SendProductsStockToN11($product),
        ];


        $batch->add($arr);
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

    public function addHbListingRecordIfNotExist($hb_listings){

        $hb_listing_id = $hb_listings['listings'][0]['listingId'];

        $is_product_exist = HBListings::where('listing_id', $hb_listing_id)->first();

        if (!$is_product_exist) {
            // Kayıt mevcut değilse yapılacak işlemler
            $hb_listing = $this->addHbListing($hb_listings['listings'][0]);
            return $hb_listing;
        }
        else{
            return $is_product_exist;
        }
    }

    public function addHbListing($hb_listing){

        $listing = HBListings::create([
            'listing_id' => $hb_listing['listingId'],
            'unique_identifier' => $hb_listing['uniqueIdentifier'] ?? '',
            'hepsiburada_sku' => $hb_listing['hepsiburadaSku'],
            'merchant_sku' => $hb_listing['merchantSku'],
            'price' => number_format((float)$hb_listing['price'], 2, '.', ''),
            'available_stock' => $hb_listing['availableStock'] ?? 0,
            'dispatch_time' => $hb_listing['dispatchTime'] ?? 0,
            'cargo_company1' => $hb_listing['cargoCompany1'] ?? '',
            'cargo_company2' => $hb_listing['cargoCompany2'] ?? '',
            'cargo_company3' => $hb_listing['cargoCompany3'] ?? '',
            'shipping_address_label' => $hb_listing['shippingAddressLabel'] ?? '',
            'shipping_profile_name' => $hb_listing['shippingProfileName'] ?? '',
            'claim_address_label' => $hb_listing['claimAddressLabel'] ?? '',
            'maximum_purchasable_quantity' => $hb_listing['maximumPurchasableQuantity'] ?? 0,
            'minimum_purchasable_quantity' => $hb_listing['minimumPurchasableQuantity'] ?? 0,
            'is_salable' => $hb_listing['isSalable'] ?? true,
            'customizable_properties' => $hb_listing['customizableProperties'] ?? [],
            'deactivation_reasons' => $hb_listing['deactivationReasons'] ?? [],
            'is_suspended' => $hb_listing['isSuspended'] ?? false,
            'is_locked' => $hb_listing['isLocked'] ?? false,
            'lock_reasons' => $hb_listing['lockReasons'] ?? [],
            'is_frozen' => $hb_listing['isFrozen'] ?? false,
            'freeze_reasons' => $hb_listing['freezeReasons'] ?? [],
            'commission_rate' => $hb_listing['commissionRate'],
            'available_warehouses' => $hb_listing['availableWarehouses'] ?? [],
            'is_fulfilled_by_hb' => $hb_listing['isFulfilledByHB'] ?? false,
            'price_increase_disabled' => $hb_listing['priceIncreaseDisabled'] ?? false,
            'price_decrease_disabled' => $hb_listing['priceDecreaseDisabled'] ?? false,
            'stock_decrease_disabled' => $hb_listing['stockDecreaseDisabled'] ?? false,
        ]);

        return $listing;

    }

    public function matchHbProduct($hb_product_id,$db_product_id){


        $relphb = RelProductsHbListings::updateOrCreate(
            ['product_id'=>$db_product_id],
            ['hb_listing_id'=>$hb_product_id]);

        $product= Products::with('coverImage','category.descendants','n11_product.n11_product','hb_product.hb_listing')->get()->find($db_product_id);

        //fiyat ve stok için job eklenecek

        return response()->json($product,200);
    }

    public function getProductBySellerCode($product_code){
        $product = Products::where('productCode',$product_code)->first();

        if (!is_null($product) || !empty($product)) {
            return Products::with('coverImage','category.descendants','n11_product.n11_product','hb_product.hb_listing')->get()->find($product->id);
        }
        else
        {
            return $product;
        }

    }
}

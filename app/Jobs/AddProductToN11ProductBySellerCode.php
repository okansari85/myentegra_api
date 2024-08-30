<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Batchable;

use App\Interfaces\IN11Api\IProduct;
use App\Models\N11Products;

use Carbon\Carbon;
use App\Enum\OrderStatusEnum;

class AddProductToN11ProductBySellerCode implements ShouldQueue
{
    use Batchable,Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $order;
    public $tries = 3;


    public function __construct($_order)
    {
        $this->order = $_order;
    }

    /**
     * Execute the job.
     */
    public function handle(IProduct $n11productService): void
    {
        //

        $order_items = $this->order->orderItemList;
        $item_is_array = is_array ($order_items->orderItem);


        if ($item_is_array){
            foreach ($order_items->orderItem as $item){
                $productSellerCode = $item->productSellerCode;
                $this->getN11Product($productSellerCode,$n11productService);
            }
        }
        else{
            //item tekli ise
            $productSellerCode = $order_items->orderItem->productSellerCode;
            $this->getN11Product($productSellerCode,$n11productService);
        }

    }

    public function getN11Product($productSellerCode,IProduct $n11productService){

        $is_product_exist = N11Products::where('productSellerCode', $productSellerCode)->first();
        if (!$is_product_exist){
        $n11_products= $n11productService->getProductBySellerCode($productSellerCode);
        $this->addN11Item($n11_products->product);
        }

    }

    public function addN11Item($n11_product){

        $product = N11Products::firstOrCreate(
            [
            'n11_id' =>  $n11_product->id
            ],
            [
            'title' => $n11_product->title,
            'display_price' => $n11_product->displayPrice,
            'price' =>  $n11_product->price,
            'productSellerCode' => $n11_product->stockItems->stockItem->sellerStockCode,
            'description' => $n11_product->description,
            'n11_category_id' => $n11_product->category->id,
            'stock_item_n11_catalog_id' => $n11_product->stockItems->stockItem->n11CatalogId,
            'stock_item_quantity' => $n11_product->stockItems->stockItem->quantity,
            'shipmentTemplate' => $n11_product->shipmentTemplate,
            'approvalStatus' => $n11_product->approvalStatus,
            'saleStatus' => $n11_product->saleStatus,
            'preparingDay' => $n11_product->preparingDay,
            'productCondition' => $n11_product->productCondition
        ]);

    }
}

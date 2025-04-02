<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Products;


use App\Interfaces\IHBApi\IListing;

class UpdateHbStocks extends Command
{

    protected $signature = 'update:hb-stocks';
    protected $description = 'Supplier id 1 olan hb stocklarını günceller';

    private IListing $listingService;

    /**
     * Execute the console command.
     */
    public function handle(IListing $_listingService)
    {

        $this->listingService = $_listingService;

        $data = Products::with('hb_product.hb_listing')->has('hb_listing')->get()->map(function ($product) {
            return [
                'hepsiburadaSku' => $product->hb_product->hb_listing->hepsiburada_sku ?? null,
                'merchantSku' => $product->hb_product->hb_listing->merchant_sku?? null,
                'availableStock' => $product->stock ?? 0,
                'maximumPurchasableQuantity' => 0
            ];
        });


        print_r($data);

        if (!empty($data)) {
            //$this->listingService->updateStock($data);
        }

    }
}

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

        $data = Products::with('hb_product.hb_listing')
        ->has('hb_product') // hb_product ilişkisi olanları filtrele
        ->get()
        ->flatMap(function ($product) {
            // Her bir hb_product için döngü başlatıyoruz
            return $product->hb_product->map(function ($hb_product) use ($product) {
                // Her hb_product için hb_listing ilişkisini alıyoruz
                $hb_listing = $hb_product->hb_listing ?? null;

                return [
                    'hepsiburadaSku' => $hb_listing->hepsiburada_sku ?? null,
                    'merchantSku' => $hb_listing->merchant_sku ?? null,
                    'availableStock' => $product->stock ?? 0,
                    'maximumPurchasableQuantity' => 0
                ];
            });
        })->toArray();

        print_r($data);

        if (!empty($data)) {
            //$this->listingService->updateStock($data);
        }

    }
}

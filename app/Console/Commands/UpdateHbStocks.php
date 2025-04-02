<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Products;

class UpdateHbStocks extends Command
{

    protected $signature = 'update:hb-stocks';
    protected $description = 'Supplier id 1 olan hb stocklarını günceller';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //

        $data = Products::with('hb_product.hb_listing')
        ->where('supplier_id', 1) // supplier_id = 1 olanları filtrele
        ->whereHas('hb_product.hb_listing') // hb_listing ilişkisi olanları filtrele
        ->get()
        ->map(function ($product) {
            $hb_listing = $product->hb_product->first()->hb_listing ?? null;
            return [
                'hepsiburadaSku' => $hb_listing->hepsiburada_sku ?? null,
                'merchantSku' => $hb_listing->merchant_sku ?? null,
                'availableStock' => $product->stock ?? 0,
                'maximumPurchasableQuantity' => 0
            ];
        })->toArray();

        print_r($data);

    }
}

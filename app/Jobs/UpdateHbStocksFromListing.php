<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Batchable;

use App\Interfaces\IHBApi\IListing;

use App\Models\Products;

class UpdateHbStocksFromListing implements ShouldQueue
{
    use Batchable,Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $product;
    public $tries = 3;
    private IListing $hbListingService;


    public function __construct($_product)
    {
        $this->product = $_product;
    }


    public function handle(IListing $hbListingService): void
    {

        $hb_listing = $this->product->hb_product;

        if ($hb_listing) {

          $hb_listing = $hb_listing->hb_listing;

          $data = [
            'hepsiburadaSku' => $hb_listing['hepsiburada_sku'],
            'merchantSku' => $hb_listing['merchant_sku'],
            'availableStock' => $this->product->stock,
            'maximumPurchasableQuantity' => 0
          ];

          $hbListingService->updateStock($data);

        } else {
            echo "İlişkili kayıt yok.";
        }

    }
}

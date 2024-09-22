<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Interfaces\IN11Api\IProductStock;

class SendProductsStocksToN11 implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $product;
    public $tries = 3;
    private IProductStock $productStockService;

    public function __construct($_product)
    {
        $this->product = $_product;
    }

    /**
     * Execute the job.
     */
    public function handle(IProductStock $productStockService): void
    {
        //
        try {

            $n11Product = $this->product['n11_product']['n11_product'];
            $n11productSellerCode = $n11Product['productSellerCode'];

            $quantity = (int) $this->product['stock'];
            $productStockService->updateStockByStockSellerCode($quantity, $n11productSellerCode);

        } catch (\Exception $e) {
            // Handle any errors gracefully
            \Log::error('Error updating product stocks: ' . $e->getMessage());
        }
    }
}

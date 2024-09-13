<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;


use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use App\Jobs\SendProductsPriceToN11;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schedule;

use App\Models\Products;

class UpdateProductsPriceToN11 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:n11-price';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'N11 Ürün Fiyat Güncellemesi';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $batch = Bus::batch([])->name('n11pricestockupdate')->dispatch();
        $products=Products::with('n11_product.n11_product')->has('n11_product')->get()->map(function ($product) {
            return new SendProductsPriceToN11($product);
            return new SendProductsStockToN11($product);
        });
        $batch->add($products);
     // Batch işlemin durumunu kontrol edelim ve çalıştığında ne yapacağımızı belirtemek için

    }
}

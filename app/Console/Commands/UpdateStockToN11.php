<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;


use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use App\Jobs\SendProductsPriceToN11;
use App\Jobs\SendProductsStocksToN11;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schedule;

use App\Models\Products;

class UpdateStockToN11 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:n11-stocks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'N11 stoklarını günceller';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //

        $batch = Bus::batch([])->name('n11pricestockupdate')->dispatch();
        $products=Products::with('n11_product.n11_product')->has('n11_product')->get()->map(function ($product) {
            return new SendProductsStocksToN11($product);
        });
        $batch->add($products);
    }
}

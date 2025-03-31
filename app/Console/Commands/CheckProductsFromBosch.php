<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\Products;


use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schedule;

use App\Jobs\CheckProuctStockPriceFromBosch;


class CheckProductsFromBosch extends Command
{

    protected $signature = 'check:bosch-products';
    protected $description = 'Bosha Bağlanıp Fiyat ve Stokları Günceller';

    // Constructor ile bağımlılık enjeksiyonu
    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {

        $products = Products::where('supplier_id', 1)->get();
        $this->info("Toplam " . $products->count() . " ürün bulundu.");

        if ($products->isEmpty()) {
            $this->warn("İşlenecek ürün bulunamadı.");
            return;
        }

        // Her ürün için ayrı job oluştur ve batch'e ekle
        Bus::batch(
            $products->map(fn($product) => new CheckProuctStockPriceFromBosch($product))->toArray()
        )->name('boschstockpriceupdate')->dispatch();

    }
}

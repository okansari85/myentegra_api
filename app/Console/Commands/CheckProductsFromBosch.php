<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\Products;


use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schedule;

use App\Jobs\CheckProuctStockPriceFromBosch;
use App\Jobs\UpdateHbStocksFromListing;


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
        //bosch urunleri supplier id = 1 olanlar bosh.com.tr den kontrol edecek

        $products = Products::where('supplier_id', 1)->get();
        $this->info("Toplam " . $products->count() . " ürün bulundu.");

        if ($products->isEmpty()) {
            $this->warn("İşlenecek ürün bulunamadı.");
            return;
        }

        // Her ürün için ayrı job oluştur ve batch'e ekle

        $props = $products->map(function ($product, $index) {
            // Her iş için 2 dakika ekliyoruz. İlk iş için 0 dakika, ikinci iş için 2 dakika, vb.
            $delayTime = now()->addSeconds(2 * $index);  // Her job arasında 2 dakika bekle

            return (new CheckProuctStockPriceFromBosch($product))
                ->delay($delayTime);  // Her job'a delay ekliyoruz
        });

        // Batch'i oluşturuyoruz ve job'ları ekliyoruz
        $batch = Bus::batch($props)
            ->name('boschstockpriceupdate')
            ->dispatch();


        /*
        $batch = Bus::batch([])->name('boschstockpriceupdate')->dispatch();

        $props=$products->map(function ($product) {
            return [
                new CheckProuctStockPriceFromBosch($product),
            ];
        });

        $batch->add($props);
        */

    }
}

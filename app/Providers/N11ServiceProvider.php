<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Interfaces\IN11Api\IOrder;
use App\Services\N11Services\OrderService;

use App\Interfaces\IN11Api\IProduct;
use App\Services\N11Services\ProductService;

use App\Interfaces\IN11Api\IProductStock;
use App\Services\N11Services\ProductStockService;

class N11ServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
        $this->app->bind(IOrder::class, OrderService::class);
        $this->app->bind(IProduct::class, ProductService::class);
        $this->app->bind(IProductStock::class, ProductStockService::class);


        $this->mergeConfigFrom(
            __DIR__.'/../../config/laravel-n11.php', 'laravel-n11'
        );
    }


    public function boot(): void
    {

        //
        $this->publishes([
            __DIR__.'/../../config/laravel-n11.php' => config_path('laravel-n11.php'),
        ], 'config');

    }
}

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Interfaces\IPazaramaApi\IBrand;
use App\Services\PazaramaServices\BrandService;

use App\Interfaces\IPazaramaApi\IOrder;
use App\Services\PazaramaServices\OrderService;

use App\Interfaces\IPazaramaApi\IProduct;
use App\Services\PazaramaServices\ProductService;

class PazaramaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {

        $this->app->bind(IBrand::class, BrandService::class);
        $this->app->bind(IOrder::class, OrderService::class);
        $this->app->bind(IProduct::class, ProductService::class);
        //
        $this->mergeConfigFrom(
            __DIR__.'/../../config/laravel-pazarama.php', 'laravel-pazarama'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
        $this->publishes([
            __DIR__.'/../../config/laravel-pazarama.php' => config_path('laravel-pazarama.php'),
        ], 'config');
    }
}

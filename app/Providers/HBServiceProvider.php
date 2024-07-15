<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Interfaces\IHBApi\IOrder;
use App\Services\HBServices\OrderService;

use App\Interfaces\IHBApi\IListing;
use App\Services\HBServices\ListingService;

use App\Interfaces\IHBApi\ICatalog;
use App\Services\HBServices\CatalogService;



class HBServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
        $this->app->bind(IOrder::class, OrderService::class);
        $this->app->bind(IListing::class, ListingService::class);
        $this->app->bind(ICatalog::class, CatalogService::class);


        $this->mergeConfigFrom(
            __DIR__.'/../../config/laravel-hb.php', 'laravel-hb'
        );
    }


    public function boot(): void
    {

        //
        $this->publishes([
            __DIR__.'/../../config/laravel-hb.php' => config_path('laravel-hb.php'),
        ], 'config');

    }
}

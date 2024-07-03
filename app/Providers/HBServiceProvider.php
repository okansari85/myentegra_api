<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Interfaces\IHBApi\IOrder;
use App\Services\HBServices\OrderService;


class HBServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
        $this->app->bind(IOrder::class, OrderService::class);


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

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;


use App\Interfaces\IProducts;
use App\Services\ProductService;

use App\Interfaces\ICategory;
use App\Services\CategoryService;

use App\Interfaces\ICargo;
use App\Services\CargoService;

use App\Interfaces\ICategoryCommision;
use App\Services\CategoryCommisionService;

use App\Interfaces\IImages;
use App\Services\ImageService;

use App\Interfaces\IDepo;
use App\Services\DepoService;

use App\Interfaces\IMalzemos;
use App\Services\MalzemosService;

use App\Interfaces\IStockMovements;
use App\Services\StockMovementsService;



use GuzzleHttp\BodySummarizer;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

        $this->app->bind(\GuzzleHttp\Client::class, function (Application $app, array $parameters = []) {
            $stack = \GuzzleHttp\HandlerStack::create();
            $stack->push(\GuzzleHttp\Middleware::httpErrors(new \GuzzleHttp\BodySummarizer(999999)), 'http_errors');
            return new \GuzzleHttp\Client(array_merge(['handler' => $stack], $parameters));
        });
        //
        $this->app->bind(IProducts::class, ProductService::class);
        $this->app->bind(ICategory::class, CategoryService::class);
        $this->app->bind(ICargo::class, CargoService::class);
        $this->app->bind(ICategoryCommision::class, CategoryCommisionService::class);
        $this->app->bind(IImages::class, ImageService::class);
        $this->app->bind(IDepo::class, DepoService::class);
        $this->app->bind(IMalzemos::class, MalzemosService::class);
        $this->app->bind(IStockMovements::class, StockMovementsService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

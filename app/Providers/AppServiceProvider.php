<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;


use App\Interfaces\IProducts;
use App\Services\ProductService;

use App\Interfaces\ICategory;
use App\Services\CategoryService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->bind(IProducts::class, ProductService::class);
        $this->app->bind(ICategory::class, CategoryService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

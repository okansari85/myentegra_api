<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Interfaces\IBotApi\IKrempl;
use App\Services\BotServices\KremplService;

use App\Interfaces\IBotApi\IBosch;
use App\Services\BotServices\BoschService;

use App\Interfaces\IBotApi\IIlacFiyati;
use App\Services\BotServices\IlacFiyatiService;

class BotServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
        $this->app->bind(IKrempl::class, KremplService::class);
        $this->app->bind(IBosch::class, BoschService::class);
        $this->app->bind(IIlacFiyati::class, IlacFiyatiService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('delete:unused-files')->daily();
        $schedule->command('fetch:tahtakale-toptan-products')->daily();
        $schedule->command('update:n11-price')->daily();
        $schedule->command('update:n11-stocks')->daily();
        $schedule->command('update:n11-orders')->everyFifteenMinutes();
        $schedule->command('update:pazarama-orders')->everyFiveMinutes();
        $schedule->command('update:hb-orders')->everyFiveMinutes();
        $schedule->command('update:hb-orders-shipped')->everyFiveMinutes();
        $schedule->command('update:hb-orders-delivered')->everyFiveMinutes();
        $schedule->command('update:hb-orders-cancelled')->everyFiveMinutes();
       // $schedule->command('fridges:fetch')->everyFifteenMinutes();
        $schedule->command('queue:restart')->everyFiveMinutes();
        $schedule->command('queue:work')->name('queue_work_name')->withoutOverlapping()->runInBackground();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

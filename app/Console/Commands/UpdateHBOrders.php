<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;

use App\Interfaces\IHBApi\IOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Carbon\Carbon;

use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

use App\Jobs\GetAndUpdateOrdersFromHb;
use App\Jobs\AddHbListingRecorIfNotExist;

class UpdateHBOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:hb-orders';
    protected $description = 'HB Sipariş Durumlarını Günceller';

    private IOrder $orderService;


    public function handle(IOrder $_orderService)
    {
        //
        $this->orderService = $_orderService;
        $this->info('HB sipariş durumlarını güncelliyor...');
        $subdays=5;

        $sdate = Carbon::now('UTC')->setTimezone('Europe/Istanbul')->format('Y-m-d H:i');
        $edate = Carbon::now('UTC')->setTimezone('Europe/Istanbul')->subDays($subdays)->format('Y-m-d H:i');

        $searchData = array(
            'offset'=> '0',
            'limit'=> '100',
            'begindate'=> $edate,
            'enddate'=>$sdate,
            'timespan'=>'24'
        );

        $hb_orders= $this->orderService->getOrders($searchData);
        $hb_orders = json_decode($hb_orders, true);

        $batch = Bus::batch([])->name('getandupdateordersfromhb')->dispatch();
        $props = array_map(function($order) {
            return [
                new AddHbListingRecorIfNotExist($order),
                new GetAndUpdateOrdersFromHb($order)
            ];
        }, $hb_orders);

        $batch->add($props);

        $this->info('HB sipariş durumları güncellendi.');
    }
}

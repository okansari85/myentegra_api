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
        $this->setOrders($this->orderService,0);
        $this->info('HB sipariş durumları güncellendi.');
    }


    public function setOrders($service,$offset){

        $subdays=1;

        $sdate = Carbon::now('UTC')->setTimezone('Europe/Istanbul')->format('Y-m-d H:i');
        $edate = Carbon::now('UTC')->setTimezone('Europe/Istanbul')->subDays($subdays)->format('Y-m-d H:i');

        $searchData = array(
            'begindate'=>$edate,
            'enddate'=>$sdate,
            'offset'=> $offset,
            'limit'=> 10,
            'page'=>$offset,
          );


        $hb_orders= $this->orderService->getOrders($searchData);



        $page_count = $hb_orders['pagecount'][0];
        $offset =  $hb_orders['offset'][0];
        $totalcount= floor($hb_orders['totalcount'][0]-10);
        $limit = $hb_orders['limit'][0];

        $hb_orders = json_decode($hb_orders['orders'], true);

        $batch = Bus::batch([])->name('getandupdateordersfromhb')->dispatch();
        $props = array_map(function($order) {
            $this->info($order['recipientName']);
            return [
                new AddHbListingRecorIfNotExist($order),
                new GetAndUpdateOrdersFromHb($order)
            ];
        }, $hb_orders);

        $batch->add($props);

        $offset+1 <= (int)$totalcount ? $this->setOrders($this->orderService,$offset+1) : null;
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Interfaces\IHBApi\IOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Carbon\Carbon;

use App\Models\Orders;

class UpdateHbOrderStatusCancelled extends Command
{

    protected $signature = 'update:hb-orders-cancelled';
    protected $description = 'Hb Siparişleri İptal Edilenleri Tespit eder.';
    private IOrder $orderService;

    public function handle(IOrder $_orderService)
    {
        $this->orderService = $_orderService;
        $this->info('Hb siparişleri İptal edilenler tespit ediliyor');
        $this->setOrderStatusToCancelled($this->orderService,0);
        $this->info('Hb İptal edilenler siparişler tespit edildi');

    }

    public function setOrderStatusToCancelled($service,$page){

        $subdays=1;

        $sdate = Carbon::now('UTC')->setTimezone('Europe/Istanbul')->format('Y-m-d H:i');
        $edate = Carbon::now('UTC')->setTimezone('Europe/Istanbul')->subDays($subdays)->format('Y-m-d H:i');

        $searchData = array(
           'begindate'=>$edate,
           'enddate'=>$sdate,
           'offset'=>$page,
           'page'=>$page,
        );

        $hb_cancelled_orders= $service->getCancelledOrders($searchData);
        $hb_cancelled_orders = json_decode($hb_cancelled_orders, true);

        print_r( $hb_cancelled_orders);
        return;

        $page_count = $hb_cancelled_orders['pageCount'];

        $orderIds = array_map(function($item) {
            return $item['orderNumber'];
        }, $hb_cancelled_orders['items']);

        // orders tablosundaki status'u 6 yap
        Orders::whereIn('market_order_number', $orderIds)
            ->update(['status' => 6]);

        $page+1 < (int)$page_count ? $this->setOrderStatusToCompleted($this->orderService,$page+1) : null;
    }
}

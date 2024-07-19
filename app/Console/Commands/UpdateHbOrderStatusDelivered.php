<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Interfaces\IHBApi\IOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Carbon\Carbon;

use App\Models\Orders;

class UpdateHbOrderStatusDelivered extends Command
{

    protected $signature = 'update:hb-orders-delivered';
    protected $description = 'Hb Siparişleri Teslim Edilenleri Tespit eder.';
    private IOrder $orderService;

    public function handle(IOrder $_orderService)
    {
        $this->orderService = $_orderService;
        $this->info('Hb siparişleri teslim edilenler tespit ediliyor');
        $this->setOrderStatusToCompleted($this->orderService,0);
        $this->info('Hb teslim edilenler siparişler tespit edildi');

    }

    public function setOrderStatusToCompleted($service,$page){

        $subdays=1;

        $sdate = Carbon::now('UTC')->setTimezone('Europe/Istanbul')->format('Y-m-d H:i');
        $edate = Carbon::now('UTC')->setTimezone('Europe/Istanbul')->subDays($subdays)->format('Y-m-d H:i');

        $searchData = array(
           'begindate'=>$edate,
           'enddate'=>$sdate,
           'offset'=>$page,
           'page'=>$page,
        );

        $hb_delivered_orders= $service->getDeliveredOrders($searchData);
        $hb_delivered_orders = json_decode($hb_delivered_orders, true);
        $page_count = $hb_delivered_orders['pageCount'];

        $orderIds = array_map(function($item) {
            return (int)$item['OrderNumber'];
        }, $hb_delivered_orders['items']);

        print_r($hb_delivered_orders);

        // orders tablosundaki status'u 4 yap
        Orders::whereIn('market_order_number', $orderIds)
            ->update(['status' => 4]);

        $page+1 < (int)$page_count ? $this->setOrderStatusToCompleted($this->orderService,$page+1) : null;
    }
}

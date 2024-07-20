<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Interfaces\IHBApi\IOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Carbon\Carbon;

use App\Models\Orders;

class UpdateHbOrderStatusShipped extends Command
{

    protected $signature = 'update:hb-orders-shipped';
    protected $description = 'Hb Siparişleri Kargolananları Tespit eder.';
    private IOrder $orderService;

    public function handle(IOrder $_orderService)
    {
        $this->orderService = $_orderService;
        $this->info('Hb siparişleri kargolananları tespit ediliyor');

        $searchData = array(
            'offset'=> '0',
            'limit'=> '100',
        );

        $hb_shipped_orders= $this->orderService->getShippedOrders($searchData);
        $hb_shipped_orders = json_decode($hb_shipped_orders, true);


        $orderIds = array_map(function($item) {
            return (int)$item['OrderNumber'];
        }, $hb_shipped_orders['items']);

        // orders tablosundaki status'u 3 yap
        Orders::whereIn('market_order_number', $orderIds)
            ->update(['status' => 3]);

        $this->info('Hb kargolanan siparişler tespit edildi');

    }
}

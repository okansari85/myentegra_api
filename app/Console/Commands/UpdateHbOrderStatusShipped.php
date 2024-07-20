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

        $payload = array_map(function($item) {
            return [
                'market_order_number' => (int)$item['OrderNumber'],
                'shippedDate' => Carbon::createFromFormat('Y-m-d\TH:i:s', $item['ShippedDate'])->format('Y-m-d H:i:s'),
                'status' => 3
        ];
        }, $hb_shipped_orders['items']);

        batch()->update(new Orders, $payload, 'market_order_number');

        $this->info('Hb kargolanan siparişler tespit edildi');

    }
}

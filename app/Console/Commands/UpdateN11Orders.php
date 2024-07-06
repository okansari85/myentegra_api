<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Http\Request;

use App\Interfaces\IN11Api\IOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Carbon\Carbon;

use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

use App\Jobs\GetAndUpdateOrders;
use App\Models\Orders;

class UpdateN11Orders extends Command
{
    protected $signature = 'update:n11-orders';
    protected $description = 'N11 Sipariş Durumlarını Günceller';

    private IOrder $orderService;

    public function handle(IOrder $_orderService)
    {
        $this->orderService = $_orderService;
        $this->info('N11 sipariş durumlarını güncelliyor...');
        $subdays=30;


        //today
        $sdate = Carbon::now('UTC')->setTimezone('Europe/Istanbul')->format('d/m/Y H:i');
        $edate = Carbon::now('UTC')->setTimezone('Europe/Istanbul')->subDays($subdays)->format('d/m/Y H:i');

        $searchData = array(
            'productId'         => '',
            'status'            => '',
            'buyerName'         => '',
            'orderNumber'       => '',
            'productSellerCode' => '',
            'recipient'         => '',
            'period'            => array (
                "startDate"  => $edate,
                "endDate"    => $sdate
            ),
            'sortForUpdateDate' => 'true',
            'updateDateSortOrder'=>'desc',
        );


        $n11_orders= $this->orderService->getOrders($searchData);
        $orders = $n11_orders->orderList->order;


        //api order ids
        $apiOrderIds  = array_map(function($order) {
            return $order->id;
        }, $orders );

        //database order ids
        $startdate = Carbon::now('UTC')->setTimezone('Europe/Istanbul')->subDays($subdays)->format('Y-m-d H:i');
        $enddate = Carbon::now('UTC')->setTimezone('Europe/Istanbul')->format('Y-m-d H:i');
        $databaseOrderIds = Orders::whereBetween('orderDate', [$startdate, $enddate])->pluck('market_order_id')->toArray();

        //compare it
        $notInApiIds = array_diff($databaseOrderIds, $apiOrderIds);

        //update it
        Orders::whereIn('market_order_id', $notInApiIds)->update(['status' => 6]);


        $batch = Bus::batch([])->name('getandupdateorders')->dispatch();

        $props = array_map(function($order){
               return new GetAndUpdateOrders($order);
        }, $orders);

        $batch->add($props);
        //return $batch;

        $this->info('N11 sipariş durumları güncellendi.');

    }
}

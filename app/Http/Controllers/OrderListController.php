<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Interfaces\IN11Api\IOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Carbon\Carbon;

use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

use App\Jobs\GetAndUpdateOrders;

class OrderListController extends Controller
{
    private IOrder $orderservice;
    //
    public function __construct(IOrder $_orderservice)
    {
        $this->orderservice = $_orderservice;
    }

    public function getOrderListFromN11(){

        //today
        $sdate = Carbon::now('UTC')->setTimezone('Europe/Istanbul')->format('d/m/Y H:i');
        $edate = Carbon::now('UTC')->setTimezone('Europe/Istanbul')->subMonths(1)->format('d/m/Y H:i');

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

        $n11_orders= $this->orderservice->getOrders($searchData);
        $orders = $n11_orders->orderList->order;

        $batch = Bus::batch([])->name('getandupdateorders')->dispatch();

        $props = array_map(function($order){
            $order_status = $order->status;
                return new GetAndUpdateOrders($order);
        }, $orders);

        $batch->add($props);
        return $batch;

    }

}

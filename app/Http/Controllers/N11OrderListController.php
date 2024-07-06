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
use App\Models\Orders;

class N11OrderListController extends Controller
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
            $edate = Carbon::now('UTC')->setTimezone('Europe/Istanbul')->subDays(5)->format('d/m/Y H:i');

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


        //return response()->json($this->orderservice->orderDetail($orders[5]->id));//$this->$orderService->orderDetail($orders[0]->id));

        //return response()->json($orders,200);

        //update order status to 0 from database
        $startDate = Carbon::now('UTC')->setTimezone('Europe/Istanbul')->subDays(5)->format('Y-m-d H:i');
        $endDate   = Carbon::now('UTC')->setTimezone('Europe/Istanbul')->format('Y-m-d H:i');


        Orders::whereBetween('orderDate', [$startDate, $endDate])->update(['status' => 0]);

        $batch = Bus::batch([])->name('getandupdateorders')->dispatch();

        $props = array_map(function($order){
               return new GetAndUpdateOrders($order);
        }, $orders);

        $batch->add($props);
        return $batch;


    }

}

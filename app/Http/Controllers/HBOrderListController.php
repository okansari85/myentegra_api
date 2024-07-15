<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Interfaces\IHBApi\IOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Carbon\Carbon;

use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

use App\Jobs\GetAndUpdateOrdersFromHb;

class HBOrderListController extends Controller
{
    //
    private IOrder $orderservice;

    public function __construct(IOrder $_orderservice)
    {
        $this->orderservice = $_orderservice;
    }

    public function getOrderListFromHB(){

        $sdate = Carbon::now('UTC')->setTimezone('Europe/Istanbul')->format('Y-m-d H:i');
        $edate = Carbon::now('UTC')->setTimezone('Europe/Istanbul')->subDays(2)->format('Y-m-d H:i');

        //return $edate;

        $searchData = array(
            'offset'=> '0',
            'limit'=> '10',
            'begindate'=> $edate,
            'enddate'=>$sdate,
            'timespan'=>'24'
        );

        $hb_orders= $this->orderservice->getOrders($searchData);
        $hb_orders = json_decode($hb_orders, true);

        $batch = Bus::batch([])->name('getandupdateordersfromhb')->dispatch();
        $props = array_map(function($order) {
            return [
                new GetAndUpdateOrdersFromHb($order)
            ];
        }, $hb_orders);
        $batch->add($props);

        return response()->json($batch,200);
    }

    public function getHBOrderDetailByOrderNumber($orderNumber){
        $hb_order_detail = $this->orderservice->getOrderDetail($orderNumber);
        $hb_order_detail = json_decode($hb_order_detail, true);

        return response()->json($hb_order_detail,200);
    }


}

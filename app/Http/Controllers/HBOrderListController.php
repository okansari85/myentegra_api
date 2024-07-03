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
        $edate = Carbon::now('UTC')->setTimezone('Europe/Istanbul')->subMonths(1)->format('Y-m-d H:i');


        $searchData = array(
            'offset'=> '0',
            'limit'=> '10',
            'begindate'=> '',
            'enddate'=>'',
        );

        $hb_orders= $this->orderservice->getOrders($searchData);
        $hb_orders = json_decode($hb_orders, true);


        return response()->json($hb_orders,200);
    }


}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Interfaces\IN11Api\IOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Carbon\Carbon;

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
            'sortForUpdateDate' => 'false',
            'updateDateSortOrder'=>'asc',
        );

        $n11_orders= $this->orderservice->getDetailedOrders($searchData);
        $orders = $n11_orders->orderList->order;

        dd($orders);
        //sipariş sayısı 0 dan büyükse
        if (count($orders)>0){
            //tüm orderleri chekck et
            $this->getN11OrderDetails($orders,0);
        }
    }

    public function getN11OrderDetails($orders,$i){
        //$this->orderservice->orderDetail($orders[$i]->id);
        return response()->json($orders[$i]);
    }
}

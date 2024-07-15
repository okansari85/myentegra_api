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
use App\Jobs\AddProductToN11ProductBySellerCode;

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
        $subdays=10;


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


        $n11_orders= $this->orderService->getDetailedOrders($searchData);
        $orders = $n11_orders->orderList->order;

        /*
        print_r($this->orderService->orderDetail($orders[0]->id));
        return;
        */

        /* İPTAL EDİLEN SİPARİŞLERİ BULAN KOD BLOĞU*/
        //api order ids
        $apiOrderIds  = array_map(function($order) {
            return $order->id;
        }, $orders );

        //database order ids
        $startdate = Carbon::now('UTC')->setTimezone('Europe/Istanbul')->subDays($subdays)->format('Y-m-d H:i');
        $enddate = Carbon::now('UTC')->setTimezone('Europe/Istanbul')->format('Y-m-d H:i');
        $databaseOrderIds = Orders::where('platformId',1)->whereBetween('orderDate', [$startdate, $enddate])->pluck('market_order_id')->toArray();

        //compare it
        $notInApiIds = array_diff($databaseOrderIds, $apiOrderIds);

        //update it
        Orders::whereIn('market_order_id', $notInApiIds)->update(['status' => 6]);

        /* İPTAL EDİLEN SİPARİŞLERİ BULAN KOD BLOĞU*/

        $batch = Bus::batch([])->name('getandupdateorders')->dispatch();
        $props = array_map(function($order) {
            return [
                new AddProductToN11ProductBySellerCode($order),
                new GetAndUpdateOrders($order)
            ];
        }, $orders);
        $batch->add($props);
        //return $batch;
        $this->info('N11 sipariş durumları güncellendi.');

    }
}

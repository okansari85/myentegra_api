<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;

use App\Interfaces\IPazaramaApi\IOrder;


use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Carbon\Carbon;

use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;


class UpdatePazaramaOrders extends Command
{

    protected $signature = 'update:pazarama-orders';
    protected $description = 'Pazarama siparişlerini kontrol eder';


    private IOrder $orderService;

    public function handle(IOrder $_orderService)
    {
        //
        $this->orderService = $_orderService;
        $this->info('Pazarama sipariş durumlarını güncelliyor...');
        $subdays=10;

        $sdate = Carbon::now('UTC')->setTimezone('Europe/Istanbul')->format('Y-m-d');
        $edate = Carbon::now('UTC')->setTimezone('Europe/Istanbul')->subDays($subdays)->format('Y-m-d');

        $searchData = array(
            "PageSize" => 500,
            "PageNumber" => 1,
            "StartDate"  => $edate,
            "EndDate"    => $sdate
        );

        $pazarama_orders= $this->orderService->getOrders($searchData);

        print_r($pazarama_orders);

    }
}

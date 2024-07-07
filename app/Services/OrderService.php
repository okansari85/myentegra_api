<?php

namespace App\Services;

use App\Interfaces\IOrder;
use App\Models\Orders;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Bus;


class OrderService implements IOrder
{
    public function getAllOrders($search,$per_page,$status){

        $status = [$status,5];

        return Orders::with('buyer')
            ->where(function ($query) use ($search,$status) {
            $query->where(DB::raw('lower(market_order_number)'), 'like', '%' . mb_strtolower($search) . '%');
            $query->whereIn('status', $status);
            })
            ->orderBy('id','desc')
           ->paginate($per_page)
           ->appends(request()->query());

    }


}

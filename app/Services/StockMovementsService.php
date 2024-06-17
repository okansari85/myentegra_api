<?php

namespace App\Services;

use App\Interfaces\IStockMovements;
use App\Models\StokHareketleri;
use Illuminate\Support\Facades\DB;

class StockMovementsService implements IStockMovements
{

    public function getStockMovements($search=null,$per_page=null,$depo_id=null){

        return response()->json(StokHareketleri::with('malzemos.raf.descendants')
        ->whereHas('malzemos', function ($query) use ($search) {
            $query->where(DB::raw('lower(productCode)'), 'like', '%' . mb_strtolower($search) . '%');
        })
        ->when($depo_id != 0, function ($query) use ($depo_id) {
            $query->whereHas('malzemos', function ($query) use ($depo_id) {
                $query->where('depo_id', '=', $depo_id);
            });
        })
        ->orderBy('id','desc')
        ->paginate($per_page)
        ->appends(request()->query()),200);

    }


}

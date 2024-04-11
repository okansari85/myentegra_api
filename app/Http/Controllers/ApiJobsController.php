<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

use App\Models\Products;
use App\Jobs\SendProductsPriceToN11;

class ApiJobsController extends Controller
{
    //
    public function addUpdateN11ProductsPriceStock($batch_id){

        return Bus::findBatch($batch_id);
    }

    public function findBatchIdByName($name){
        $pendingBatch = DB::table('job_batches')
                     ->where('name', $name)
                     ->where('pending_jobs', '>', 0)
                     ->first();

            if ($pendingBatch)  {
            return Bus::findBatch($pendingBatch->id);
            } else{
                return response()->json(['message' => "No pending batch found with the name: {$name}."],200);
            }
    }

    public function updateN11Prices(){

        $batch = Bus::batch([])->name('n11pricestockupdate')->dispatch();
        $products=Products::with('n11_product.n11_product')->has('n11_product')->get()->map(function ($product) {
            return new SendProductsPriceToN11($product);
        });
        $batch->add($products);
        return $batch;

    }
}

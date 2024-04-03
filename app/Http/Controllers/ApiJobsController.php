<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

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
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Interfaces\IHakedis;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Carbon\Carbon;
use Validator;


class HakedisController extends Controller
{
    //

    private IHakedis $hakedisService;

    public function __construct(IHakedis $_hakedisService){
        $this->hakedisService = $_hakedisService;
    }

    public function addHakedisItem(Request $request){
        $order_id=$request->orderID;

        try {
            $hakedis_item = $this->hakedisService->addHakedisItem($order_id);
            // Return a 200 OK response with the updated product data
            return response()->json($hakedis_item, 200);
        } catch (\Exception $e) {
            // If an exception occurs, return a 500 Internal Server Error response with the exception message
            return response()->json(['error' => 'An error occurred while adding the product to stock.', 'message' => $e->getMessage()], 500);
        }

    }

    public function listHakedisByDay(){
        $hakedisler = $this->hakedisService->listHakedisByDay();
        return response()->json($hakedisler,200);
    }
}

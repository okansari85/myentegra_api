<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Interfaces\IOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Carbon\Carbon;
use Validator;


use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    //

    private IOrder $orderService;

    public function __construct(IOrder $_orderService)
    {
        $this->orderService = $_orderService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $search = $request->query('search');
        $per_page = $request->query('per_page');
        $status = $request->query('status');

        return response()->json($this->orderService->getAllOrders($search,$per_page,$status));
    }
}

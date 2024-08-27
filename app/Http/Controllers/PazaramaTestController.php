<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Interfaces\IPazaramaApi\IBrand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PazaramaTestController extends Controller
{
    //
    private IBrand $brandService;

    public function __construct(IBrand $_brandService)
    {
        $this->brandService = $_brandService;
    }

    public function getBrands()
    {
        $searchData = array(
            'page'=> '2',
            'size'=> '100',
        );

        $pazarama_brands= $this->brandService->getBrands($searchData);
        return response()->json($pazarama_brands,200);

    }
}

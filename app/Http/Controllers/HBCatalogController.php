<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Interfaces\IHBApi\ICatalog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Carbon\Carbon;

class HBCatalogController extends Controller
{
    //

    private ICatalog $catalogService;

    public function __construct(ICatalog $_catalogService)
    {
        $this->catalogService = $_catalogService;
    }

    public function getAllProductsFromHB(Request $request){

        $searchData = array(
            'page'=> 0,
            'size'=> 10,
            'barcode'=> '',
            'merchantSku'=>'',
            'hbSku'=> '',
        );



        $hb_products= $this->catalogService->getAllProducts($searchData);
        $hb_products = json_decode($hb_products, true);
        return response()->json($hb_products,200);

    }

    public function getAllProductsFromHBByStatus(){

        $searchData = array(
            'productStatus'=> 'MATCHED',
            'page'=> 0,
            'size'=> 1000,
            'version'=> 1,
            'taskStatus' => true
        );



        $hb_products= $this->catalogService->getAllProducts($searchData);
        $hb_products = json_decode($hb_products, true);
        return response()->json($hb_products,200);


    }
}


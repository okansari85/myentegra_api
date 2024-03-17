<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Interfaces\IN11Api\IProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Carbon\Carbon;

class N11ProductController extends Controller
{

    private IProduct $productservice;
    //
    public function __construct(IProduct $_productservice)
    {
        $this->productservice = $_productservice;
    }

    public function getN11ProductBySellerCode($sellercode,Request $request){

        $n11_products= $this->productservice->getProductBySellerCode($sellercode);
        return $n11_products;
    }


}

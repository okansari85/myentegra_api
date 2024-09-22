<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Interfaces\IN11Api\IProduct;
use App\Interfaces\IN11Api\IProductStock;


use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Carbon\Carbon;

class N11ProductController extends Controller
{

    private IProduct $productservice;
    private IProductStock $productstockservice;
    //
    public function __construct(IProduct $_productservice, IProductStock $_productstockservice)
    {
        $this->productservice = $_productservice;
        $this->productstockservice = $_productstockservice;
    }

    public function getN11ProductBySellerCode($sellercode,Request $request){

        $n11_products= $this->productservice->getProductBySellerCode($sellercode);
        return $n11_products;
    }

    public function updateStockByStockSellerCode($sellercode,Request $request){

        $n11_products= $this->productstockservice->updateStockByStockSellerCode(30,$sellercode);
        return $n11_products;
    }



}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Interfaces\ICategoryCommision;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Carbon\Carbon;

class CategoryComissionController extends Controller
{
    //

    private ICategoryCommision $categorycommisionservice;

    public function __construct(ICategoryCommision $_categorycommisionservice)
    {
        $this->categorycommisionservice = $_categorycommisionservice;
    }

    public function getN11CategoryCommisionsFromN11(){

        return $this->categorycommisionservice->getN11CategoryCommisionsFromN11();
    }

    public function getN11CommissionRates(Request $request){

        $search = $request->query('search');
        $per_page = $request->query('per_page');

        return $this->categorycommisionservice->getN11CommissionRates($search,$per_page);
    }

    public function getN11CategoryCommissionByCategoryId($n11_category_id){

        return $this->categorycommisionservice->getN11CategoryCommissionByCategoryId($n11_category_id);
    }

}

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

    public function getN11CategoryCommisions(){

        return $this->categorycommisionservice->getN11CategoryCommisions();
    }

}

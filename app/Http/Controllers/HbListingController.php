<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Interfaces\IHBApi\IListing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Carbon\Carbon;

class HbListingController extends Controller
{
    //

    private IListing $listingService;

    public function __construct(IListing $_listingService)
    {
        $this->listingService = $_listingService;
    }

    public function getListingFromHb(){

        $searchData = array(
            'offset'=> '0',
            'limit'=> '5000',
            'hbSkuList'=> '',
        );

        $hb_listings= $this->listingService->getListings($searchData);
        $hb_listings = json_decode($hb_listings, true);



        return response()->json($hb_listings,200);
    }
}

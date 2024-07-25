<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Interfaces\IHBApi\IListing;
use App\Interfaces\IProducts;



use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Carbon\Carbon;

use App\Exceptions\HBException;

class HbListingController extends Controller
{
    //

    private IListing $listingService;
    private IProducts $productService;

    public function __construct(IListing $_listingService,IProducts $_productService)
    {
        $this->listingService = $_listingService;
        $this->productService = $_productService;
    }

    public function getListingFromHb($hbSku=''){

        $searchData = array(
            'offset'=> '0',
            'limit'=> '5000',
            'hbSkuList'=> $hbSku,
        );

        $hb_listings= $this->listingService->getListings($searchData);
        $hb_listings = json_decode($hb_listings, true);

        return response()->json($hb_listings,200);
    }

    public function getHbListingFromHbBySku($hbSku,Request $request){

        $searchData = array(
            'offset'=> '0',
            'limit'=> '5000',
            'hbSkuList'=> $hbSku,
        );

        $hb_listings= $this->listingService->getListings($searchData);
        $hb_listings = json_decode($hb_listings, true);

        return $hb_listings;
    }

    public function getHbListingByMerchantSku($merchantSku=''){

        if (empty($merchantSku)) {
            return response()->json(['error' => 'Ürün Bulunamadı'], 404);
        }

        try{
            $searchData = array(
                'offset'=> '0',
                'limit'=> '5000',
                'merchantSkuList'=> $merchantSku,
            );

            $hb_listings= $this->listingService->getListings($searchData);
            $hb_listings = json_decode($hb_listings, true);

            $hb_listing = $this->productService->addHbListingRecordIfNotExist($hb_listings);

            return $hb_listing;

        }
        catch(\Exception $e){
            return response()->json(['message'=>$e->getMessage()],$e->getCode());
        }



    }
}

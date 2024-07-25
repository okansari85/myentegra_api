<?php

namespace App\Services\HBServices;


use App\Exceptions\HBException;
use App\Services\HBService;
use App\Interfaces\IHBApi\IListing;
use Illuminate\Support\Arr;


class ListingService extends HBService implements IListing
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getListings(array $data=[]){

        $queryString = Arr::query($data);
        $url = self::END_POINT.'/listings/merchantid/'.$this->_merchantID.'?'.$queryString;
        $response = $this->_client->request('GET',$url);
        return $response->getBody();
    }


}

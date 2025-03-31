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

    public function updateStock(array $data=[]){

        $url = self::END_POINT.'/listings/merchantid/'.$this->_merchantID.'/stock-uploads';

        $headers = $this->_parameters['headers'];
        $headers['content-type'] = 'application/*+json'; // JSON olarak gönderildiğinden emin ol

        $response = $this->_client->request('POST',$url,[
            'headers' => $headers,
            'body' => json_encode([$data])
        ]);

        return json_decode($response->getBody(), true);
    }


}

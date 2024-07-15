<?php

namespace App\Services\HBServices;

use App\Exceptions\HBException;
use App\Services\HBService;
use App\Interfaces\IHBApi\ICatalog;
use Illuminate\Support\Arr;


class CatalogService extends HBService implements ICatalog
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getAllProducts(array $data=[]){
        $queryString = Arr::query($data);
        $url = self::END_POINT.'/product/api/products/all-products-of-merchant/'.$this->_merchantID.'?'.$queryString;
        $response = $this->_client->request('GET',$url);
        return $response->getBody();
    }

    public function getProductsByStatus(array $data=[]){
        $queryString = Arr::query($data);
        /*/product/api/products/products-by-merchant-and-status?productStatus=CREATED&version=1&page=0&size=1000' \*/
        $url = self::END_POINT.'/product/api/products/products-by-merchant-and-status?merchantId='.$this->_merchantID.'&'.$queryString;
        $response = $this->_client->request('GET',$url);
        return $response->getBody();
    }

}

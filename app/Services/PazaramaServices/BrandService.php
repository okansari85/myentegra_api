<?php

namespace App\Services\PazaramaServices;

use App\Exceptions\PazaramaException;
use App\Services\PazaramaService;
use App\Interfaces\IPazaramaApi\IBrand;
use Illuminate\Support\Arr;

class BrandService extends PazaramaService implements IBrand
{

    protected $_client;

    public function __construct()
    {
        parent::__construct();
        $this->_client = $this->setEndPoint(self::END_POINT);
    }

    public function getBrands(array $data=[]){
        $queryString = Arr::query($data);
        $url = self::END_POINT.'/getBrands?'.$queryString;
        $response = $this->_client->request('GET',$url);
        return json_decode($response->getBody()->getContents(), true);
    }


}

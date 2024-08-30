<?php

namespace App\Services\PazaramaServices;

use App\Exceptions\PazaramaException;
use App\Services\PazaramaService;
use App\Interfaces\IPazaramaApi\IProduct;
use Illuminate\Support\Arr;
use Exception;


class ProductService extends PazaramaService implements IProduct
{

    protected $_client;

    public function __construct()
    {
        parent::__construct();
        $this->_client = $this->setEndPoint(self::END_POINT);
    }

    public function getProductByCode(array $data=[]){
        $url = self::END_POINT.'/getProductDetail';
        $response = $this->_client->request('POST',$url,[
            'json' => $data
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }
}

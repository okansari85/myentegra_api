<?php

namespace App\Services\PazaramaServices;

use App\Exceptions\PazaramaException;
use App\Services\PazaramaService;
use App\Interfaces\IPazaramaApi\IOrder;
use Illuminate\Support\Arr;
use Exception;


class OrderService extends PazaramaService implements IOrder
{

    protected $_client;

    public function __construct()
    {
        parent::__construct();
        $this->_client = $this->setEndPoint(self::END_POINT);
    }

    public function getOrders(array $data=[]){
        $url = self::END_POINT.'/getOrdersForApi';
        $response = $this->_client->request('POST',$url,[
            'json' => $data
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }
}

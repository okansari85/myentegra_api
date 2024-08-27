<?php

namespace App\Services\PazaramaServices;

use App\Exceptions\PazaramaException;
use App\Services\PazaramaService;
use App\Interfaces\IPazaramaApi\IOrder;
use Illuminate\Support\Arr;



class OrderService extends PazaramaService implements IOrder
{

    protected $_client;

    public function __construct()
    {
        parent::__construct();
        $this->_client = $this->setEndPoint(self::END_POINT);
    }

    public function getOrders(array $data=[]){
        $url = 'https://isortagimapi.pazarama.com/order/getOrdersForApi';
        $this->_parameters['form_params'] = $data;
        $response = $this->_client->request('POST',$url);
        return json_decode($response->getBody()->getContents(), true);
    }


}

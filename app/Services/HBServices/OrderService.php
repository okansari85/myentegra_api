<?php

namespace App\Services\HBServices;


use App\Exceptions\HBException;
use App\Services\HBService;
use App\Interfaces\IHBApi\IOrder;
use Illuminate\Support\Arr;



class OrderService extends HBService implements IOrder
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getOrders(array $data = []) {
        $queryString = Arr::query($data);
        $url = self::END_POINT.'/packages/merchantid/'.$this->_merchantID.'?'.$queryString;
        $response = $this->_client->request('GET',$url);
        return $response->getBody();
    }

    public function getOrderDetail($orderNumber){
    //orders/merchantid/{merchantId}/ordernumber/{orderNumber}
        $url = self::END_POINT.'/orders/merchantid/'.$this->_merchantID.'/ordernumber/'.$orderNumber;
        $response = $this->_client->request('GET',$url);
        return $response->getBody();
    }




}

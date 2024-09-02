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
        return $response = [
            'pagecount'=> $response->getHeaders()['pagecount'],
            'offset'=> $response->getHeaders()['offset'],
            'limit'=> $response->getHeaders()['limit'],
            'totalcount'=>$response->getHeaders()['totalcount'],
            'orders'=> $response->getBody()->getContents()
        ];
    }

    public function getOrderDetail($orderNumber){
        //orders/merchantid/{merchantId}/ordernumber/{orderNumber}
        $url = self::END_POINT.'/orders/merchantid/'.$this->_merchantID.'/ordernumber/'.$orderNumber;
        $response = $this->_client->request('GET',$url);
        return $response->getBody();
    }

    public function getShippedOrders(array $data = []){
        $queryString = Arr::query($data);
        //packages/merchantid/merchantId/shipped?offset=0&limit=100
        $url = self::END_POINT.'/packages/merchantid/'.$this->_merchantID.'/shipped?'.$queryString;
        $response = $this->_client->request('GET',$url);
        return $response->getBody();
    }

    public function getDeliveredOrders(array $data = []){
        $queryString = Arr::query($data);
        //packages/merchantid/merchantId/delivered?offset=0&limit=100
        $url = self::END_POINT.'/packages/merchantid/'.$this->_merchantID.'/delivered?'.$queryString;
        $response = $this->_client->request('GET',$url);
        return $response->getBody();
    }

    public function getCancelledOrders(array $data = []){
    //orders/merchantid/merchantId/cancelled
         $queryString = Arr::query($data);
        $url = self::END_POINT.'/orders/merchantid/'.$this->_merchantID.'/cancelled?'.$queryString;
        $response = $this->_client->request('GET',$url);
        return $response->getBody();
    }

}

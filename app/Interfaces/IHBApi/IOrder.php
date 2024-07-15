<?php

namespace App\Interfaces\IHBApi;

interface IOrder
{

    public const END_POINT = "https://oms-external.hepsiburada.com";

    public function getOrders(array $data = []);
    public function getOrderDetail($orderNumber);
}

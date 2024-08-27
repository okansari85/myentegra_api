<?php

namespace App\Interfaces\IPazaramaApi;

interface IOrder
{

    public const END_POINT = "/order";
    public function getOrders(array $data=[]);

}

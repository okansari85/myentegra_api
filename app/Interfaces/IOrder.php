<?php

namespace App\Interfaces;

interface IOrder
{
    public function getAllOrders($search,$per_page,$status);

}

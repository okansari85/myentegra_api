<?php

namespace App\Interfaces;

interface IOrder
{
    public function getAllOrders($search,$per_page,$status);
    public function getConfirmedOrders($search,$per_page,$status);
    public function confirmItem($item_id,$product_id);
}

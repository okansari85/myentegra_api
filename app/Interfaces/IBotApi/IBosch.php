<?php

namespace App\Interfaces\IBotApi;

interface IBosch
{
    public const END_POINT = "https://www.bosch-home.com.tr/tr/product";
    public function getProduct($productID = null);
}

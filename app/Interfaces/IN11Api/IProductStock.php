<?php

namespace App\Interfaces\IN11Api;


interface IProductStock{
    public const END_POINT = "ProductStockService.wsdl";

    public function updateStockByStockSellerCode(int $quantity,string $sellerStockCode = null,): object;
}

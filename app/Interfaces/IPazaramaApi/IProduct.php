<?php

namespace App\Interfaces\IPazaramaApi;

interface IProduct
{

    public const END_POINT = "/product";
    public function getProductByCode(array $data=[]);

}

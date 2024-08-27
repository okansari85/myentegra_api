<?php

namespace App\Interfaces\IPazaramaApi;

interface IBrand
{

    public const END_POINT = "/brand";
    public function getBrands(array $data=[]);

}

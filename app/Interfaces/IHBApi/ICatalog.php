<?php

namespace App\Interfaces\IHBApi;

interface ICatalog
{

    public const END_POINT = "https://mpop.hepsiburada.com";
    public function getAllProducts(array $data=[]);
    public function getProductsByStatus(array $data=[]);
}

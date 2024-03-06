<?php

namespace App\Interfaces;

interface IProducts
{
    public function getAllProducts($search,$per_page);
    public function addProductCoverImage($file,$product_id);
}

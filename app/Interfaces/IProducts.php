<?php

namespace App\Interfaces;

interface IProducts
{
    public function getAllProducts($search,$per_page);
    public function addProductCoverImage($file,$product_id);
    public function matchN11Product($n11_product, $db_product);
}

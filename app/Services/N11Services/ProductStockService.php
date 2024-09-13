<?php

namespace App\Services\N11Services;

use App\Exceptions\N11Exception;
use App\Interfaces\IN11Api\IProductStock;
use App\Services\N11Service;
use SoapClient;

class ProductService extends N11Service implements IProduct
{
    private $_client;

    public function __construct()
    {
        parent::__construct();
        $this->_client = $this->setEndPoint(self::END_POINT);
    }

    //UpdateStockByStockSellerCode
    public function updateStockByStockSellerCode(int $quantity,string $sellerStockCode = null,): object
    {

        $this->_parameters["stockItems"] = [
            "stockItem" => [
                "sellerStockCode" => $productSellerCode,
                "quantity" => $quantity,
            ]

        ];

        return $this->_client->UpdateStockByStockSellerCode($this->_parameters);
    }


}

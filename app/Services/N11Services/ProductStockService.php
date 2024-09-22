<?php

namespace App\Services\N11Services;

use App\Exceptions\N11Exception;
use App\Interfaces\IN11Api\IProductStock;
use App\Services\N11Service;
use SoapClient;

class ProductStockService extends N11Service implements IProductStock
{
    private $_client;

    public function __construct()
    {
        parent::__construct();
        $this->_client = $this->setEndPoint(self::END_POINT);
    }


    public function getProductStockBySellerCode (string $productSellerCode): object
    {
        $this->_parameters["productSellerCode"] = $productSellerCode;
        return $this->_client->GetProductStockBySellerCode($this->_parameters);
    }

    //UpdateStockByStockSellerCode
    public function updateStockByStockSellerCode(int $quantity,string $sellerStockCode = null): object
    {

        $this->_parameters["stockItems"] = [
            "stockItem" => [
                "sellerStockCode" => $sellerStockCode,
                "quantity" => (int) $quantity,
                "version" => ""
            ]

        ];

        return $this->_client->UpdateStockByStockSellerCode($this->_parameters);
    }


}

<?php

namespace App\Services\N11Services;

use App\Exceptions\N11Exception;
use App\Interfaces\IN11Api\IProduct;
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

    public function getProductByProductId(int $productId): object
    {
        $this->_parameters["productId"] = $productId;
        return $this->_client->GetProductByProductId($this->_parameters);
    }

    public function getProductBySellerCode(string $productSellerCode): object
    {
        $this->_parameters["sellerCode"] = $productSellerCode;
        return $this->_client->GetProductBySellerCode($this->_parameters);
    }

    public function getProductList(int $currentPage = 1, int $pageSize = self::GENERAL_LIMIT): object
    {
        $this->_parameters["pagingData"] = [
            "currentPage" => $currentPage,
            "pageSize" => $pageSize
        ];
        return $this->_client->GetProductList($this->_parameters);
    }

    public function saveProduct(array $data): object
    {
        $this->_parameters["product"] = $data;
        return $this->_client->SaveProduct($this->_parameters);
    }

    public function searchProducts(int $currentPage = 1, int $pageSize = self::GENERAL_LIMIT, string $keyword = null, $saleStartDate = null, $saleEndDate = null, $approvalStatus = self::ACTIVE): object
    {
        $this->_parameters["pagingData"] = [
            "currentPage" => $currentPage,
            "pageSize" => $pageSize
        ];
        $this->_parameters["productSearch"] = [
            "name" => $keyword,
            "saleDate" => [
                "startDate" => $saleStartDate,
                "endDate" => $saleEndDate,
            ]
        ];
        $this->_parameters["approvalStatus"] = $approvalStatus;
        return $this->_client->SearchProducts($this->_parameters);
    }


    public function deleteProductById(int $productId): object
    {
        $this->_parameters["productId"] = $productId;
        $this->_client->DeleteProductById($this->_parameters);
    }

    public function deleteProductBySellerCode(string $productSellerCode): object
    {
        $this->_parameters["productSellerCode"] = $productSellerCode;
        $this->_client->DeleteProductBySellerCode($this->_parameters);
    }

    public function updateDiscountValueByProductId(int $productId, int $discountType = self::DISCOUNT_AMOUNT, float $discountValue = 0, string $startDate = null, string $endDate = null): object
    {
        $this->_parameters["productId"] = $productId;
        $this->_parameters["discountType"] = $discountType;
        $this->_parameters["productDiscount"] = [
            "discountValue" => $discountValue,
            "discountStartDate" => $startDate,
            "discountEndDate" => $endDate,
        ];
        return $this->_client->UpdateDiscountValueByProductId($this->_parameters);
    }

    public function updateDiscountValueByProductSellerCode(string $productSellerCode, int $discountType = self::DISCOUNT_AMOUNT, float $discountValue = 0, string $startDate = null, string $endDate = null): object
    {
        $this->_parameters["productSellerCode"] = $productSellerCode;
        $this->_parameters["discountType"] = $discountType;
        $this->_parameters["productDiscount"] = [
            "discountValue" => $discountValue,
            "discountStartDate" => $startDate,
            "discountEndDate" => $endDate,
        ];
        return $this->_client->UpdateDiscountValueByProductSellerCode($this->_parameters);
    }


    public function updateProductPriceById(int $productId, float $price, $currencyType = self::TL, string $sellerStockCode = null, float $optionPrice = null): object
    {
        $this->_parameters["productId"] = $productId;
        $this->_parameters["price"] = $price;
        $this->_parameters["currencyType"] = $currencyType;
        $this->_parameters["stockItems"] = [
                "stockItem" => [
                    "sellerStockCode" => $sellerStockCode,
                    "optionPrice" => $optionPrice,
                ]

        ];
        return $this->_client->UpdateProductPriceById($this->_parameters);
    }


    public function updateProductPriceBySellerCode(string $productSellerCode, float $price, $currencyType = self::TL, string $sellerStockCode = null, float $optionPrice = null): object
    {
        $this->_parameters["productSellerCode"] = $productSellerCode;
        $this->_parameters["price"] = $price;
        $this->_parameters["currencyType"] = $currencyType;
        $this->_parameters["product"] = [
            "stockItems" => [
                "stockItem" => [
                    "sellerStockCode" => $sellerStockCode,
                    "optionPrice" => $optionPrice,
                ]
            ]
        ];
        return $this->_client->UpdateProductPriceBySellerCode($this->_parameters);
    }


    public function updateProductBasic(array $data): object
    {
        $this->_parameters["productId"] = $data["productId"];
        $this->_parameters["productSellerCode"] = $data["productSellerCode"];
        $this->_parameters["price"] = $data["price"];
        $this->_parameters["description"] = $data["description"];
        $this->_parameters["images"] = [
            "image" => $data["images"]
        ];
        $this->_parameters["productDiscount"] = [
            "discountType" => $data["discountType"],
            "discountValue" => $data["discountValue"],
            "discountStartDate" => $data["startDate"],
            "discountEndDate" => $data["discountEndDate"],
            "discountStockCode" => $data["discountStockCode"],
            "optionPrice" => $data["optionPrice"],
            "quantity" => $data["quantity"],
        ];
        $this->_parameters["stockItems"] = [
            "stockItem" => $data["stockItems"]
        ];
        return $this->_client->UpdateProductBasic($this->_parameters);
    }

    /**
     * @param int $productId
     * @param string $buyerEmail
     * @param string $subject
     * @param $status
     * @param string $questionDate
     * @param int $currentPage
     * @param int $pageSize
     * @return object
     * @description Müşterileriniz tarafından mağazanıza sorulan soruları listeler.
     * Sorularınızı listelemek için Appkey ve Appsecret bilgileriniz gerekmektedir.
     */
    public function getProductQuestionList(int $productId, string $buyerEmail, string $subject, $status, string $questionDate, int $currentPage = 1, int $pageSize = self::GENERAL_LIMIT): object {
        $this->_parameters["currentPage"] = $currentPage;
        $this->_parameters["pageSize"] = $pageSize;
        $this->_parameters["productQuestionSearch"] = [
            "productId" => $productId,
            "buyerEmail" => $buyerEmail,
            "subject" => $subject,
            "status" => $status,
            "questionDate" => $questionDate,
        ];
        return $this->_client->GetProductQuestionList($this->_parameters);
    }


    public function getProductQuestionDetail(int $productQuestionId): object {
        $this->_parameters["productQuestionId"] = $productQuestionId;
        return $this->_client->GetProductQuestionDetail($this->_parameters);
    }


    public function saveProductAnswer(int $productQuestionId, string $productAnswer): object {
        $this->_parameters["productQuestionId"] = $productQuestionId;
        $this->_parameters["productAnswer"] = $productAnswer;
        return $this->_client->SaveProductAnswer($this->_parameters);
    }


    public function productAllStatusCountsRequest(): object {
        return $this->_client->ProductAllStatusCountsRequest($this->_parameters);
    }


}

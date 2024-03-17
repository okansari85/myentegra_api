<?php

namespace App\Interfaces\IN11Api;

interface IProduct{

    public const END_POINT = "ProductService.wsdl";
    public const ACTIVE = "Active";
    public const SUSPENDED = "Suspended";
    public const PROHIBITED = "Prohibited";
    public const UNLISTED = "Unlisted";
    public const WAITING_FOR_APPROVAL = "WaitingForApproval";
    public const REJECTED = "Rejected";
    public const UNAPPROVED_UPDATE = "UnapprovedUpdate";
    public const DISCOUNT_AMOUNT = 1;
    public const DISCOUNT_PERCENT = 2;
    public const DISCOUNTED_PRICE = 3;
    public const TL = 1;
    public const USD = 2;
    public const EURO = 3;



    public function getProductByProductId(int $productId): object;
    public function getProductBySellerCode(string $productSellerCode): object;
    public function getProductList(int $currentPage = 1, int $pageSize = 100): object;
    public function saveProduct(array $data): object;
    public function searchProducts(int $currentPage = 1, int $pageSize = 100, string $keyword = null, $saleStartDate = null, $saleEndDate = null, $approvalStatus = self::ACTIVE): object;
    public function deleteProductById(int $productId): object;
    public function deleteProductBySellerCode(string $productSellerCode): object;
    public function updateDiscountValueByProductId(int $productId, int $discountType = self::DISCOUNT_AMOUNT, float $discountValue = 0, string $startDate = null, string $endDate = null): object;
    public function updateDiscountValueByProductSellerCode(string $productSellerCode, int $discountType = self::DISCOUNT_AMOUNT, float $discountValue = 0, string $startDate = null, string $endDate = null): object;
    public function updateProductPriceById(int $productId, float $price, $currencyType = self::TL, string $sellerStockCode = null, float $optionPrice = null): object;
    public function updateProductPriceBySellerCode(string $productSellerCode, float $price, $currencyType = self::TL, string $sellerStockCode = null, float $optionPrice = null): object;
    public function updateProductBasic(array $data): object;
    public function getProductQuestionList(int $productId, string $buyerEmail, string $subject, $status, string $questionDate, int $currentPage = 1, int $pageSize = self::GENERAL_LIMIT): object;
    public function getProductQuestionDetail(int $productQuestionId): object;
    public function saveProductAnswer(int $productQuestionId, string $productAnswer): object;
    public function productAllStatusCountsRequest(): object;

}

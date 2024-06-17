<?php

namespace App\Interfaces;

interface IMalzemos
{
    public function getMalzemos($search=null,$per_page=null,$depo_id=null);
    public function getMalzemosByProductCode($productCode,$depoId);
    public function addProductToStock(int $product_id, int $quantity);
    public function deleteStockMovement(int $stockMovementId);
    public function removeProductFromStock(int $product_id, int $quantity);
    public function saveProduct($productData);
    public function deleteProductById($id);
    public function updateProductById($productId, $updatedData);
}

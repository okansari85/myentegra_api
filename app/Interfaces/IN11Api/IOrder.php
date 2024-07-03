<?php

namespace App\Interfaces\IN11Api;

interface IOrder
{

    public const END_POINT = "OrderService.wsdl";
    public const OUT_OF_STOCK = "OUT_OF_STOCK";
    public const OTHER = "OTHER";




    public function getOrderDetail(array $searchData = []): object;
    public function getOrders(array $searchData = []): object;
    public function getDetailedOrders(array $searchData = []): object;
    public function orderDetail(int $orderId): object;
    public function orderItemAccept(int $orderId, int $numberOfPackages): object;
    public function orderItemReject(int $orderId, string $rejectReason, string $rejectReasonType = self::OUT_OF_STOCK): object;
    public function makeOrderItemShipment(int $orderId): object;
}

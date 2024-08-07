<?php
namespace App\Enum;


enum OrderStatusEnum:int
{
    case NEW_ORDER = 1;
    case READY_TO_SHIP = 2;
    case SHIPPED = 3;
    case COMPLETED = 4;
    case DELAYED_SHIPPING = 5;
    case CANCELED = 6;
}

<?php
namespace App\Enum;


enum OrderApproveStatusEnum:int
{
    case APPROVED = 1;
    case REJECTED = 2;
}

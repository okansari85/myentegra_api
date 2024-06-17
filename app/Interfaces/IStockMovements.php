<?php

namespace App\Interfaces;

interface IStockMovements
{
    public function getStockMovements($search=null,$per_page=null,$depo_id=null);

}

<?php

namespace App\Interfaces;


interface ICategoryCommision
{

    public function getN11CategoryCommisionsFromN11();
    public function createCategoryNode($cat4,$cat3,$cat2,$cat1);
    public function getN11CommissionRates($search,$per_page);
    public function getN11CategoryCommissionByCategoryId($n11_category_id);

}

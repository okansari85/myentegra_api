<?php

namespace App\Interfaces;

interface ICargo
{
    public function getCargoPricesFromN11();
    public function importHbCargoPricesFromFile($file);

}

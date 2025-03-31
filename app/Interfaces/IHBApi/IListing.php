<?php

namespace App\Interfaces\IHBApi;

interface IListing
{

    public const END_POINT = "https://listing-external.hepsiburada.com";
    public function getListings(array $data=[]);
    public function updateStock(array $data=[]);
}

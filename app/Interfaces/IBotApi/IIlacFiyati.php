<?php

namespace App\Interfaces\IBotApi;

interface IIlacFiyati
{
    public const END_POINT = "https://ilacfiyati.com";
    public function getMedicines($page=null);
}

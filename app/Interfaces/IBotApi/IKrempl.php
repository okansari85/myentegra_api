<?php

namespace App\Interfaces\IBotApi;

interface IKrempl
{
    public const END_POINT = "https://krempl.com/marken/bosch";
    public function getFridges($page=null);
}

<?php

namespace App\Exceptions;

use Exception;

class N11Exception extends Exception
{
    //
    public function render($request)
    {
        return response()->json(["error" => true, "message" => $this->getMessage()]);
    }
}

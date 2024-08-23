<?php

namespace App\Services\BotServices;

use App\Exceptions\BotException;
use App\Services\BotService;
use App\Interfaces\IBotApi\IBosch;
use Illuminate\Support\Arr;


class BoschService extends BotService implements IBosch
{

    public function __construct()
    {
        parent::__construct();
    }

}

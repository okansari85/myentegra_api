<?php

namespace App\Services;

use App\Exceptions\N11Exception;
use SoapClient;

class N11Service
{

    public const GENERAL_LIMIT = 100;
    protected $_parameters = null;
    private $_baseUrl = "https://api.n11.com/ws";


    public function __construct()
    {

        if (is_null(config("laravel-n11.api_key")) || is_null(config("laravel-n11.api_secret"))) {
            {
                throw new N11Exception("API KEY or API SECRET cannot be null");
            }
        }
        $this->_parameters = ['auth' => ['appKey' => config("laravel-n11.api_key"), 'appSecret' => config("laravel-n11.api_secret")]];
    }

    protected function setEndPoint(string $endPoint): SoapClient
    {
        return new SoapClient($this->_baseUrl . "/" . $endPoint);
    }

}

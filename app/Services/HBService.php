<?php

namespace App\Services;
use App\Exceptions\HBException;
use GuzzleHttp\Client;



class HBService
{
    protected $_parameters = null;
    protected $_client;
    protected $_merchantID;

    public function __construct()
    {

        if (is_null(config("laravel-hb.username")) || is_null(config("laravel-hb.servisAnahtari")) || is_null(config("laravel-hb.password" )) || is_null(config("laravel-hb.merchantId" ))) {
            {
                throw new HBException("Some Credientals Not Provideds");
            }
        }

        $this->_merchantID = config("laravel-hb.merchantId");

        $this->_parameters = [
            'headers' => [
            'accept' => 'application/json',
            'authorization' => 'Basic ' .  base64_encode(config("laravel-hb.merchantId") . ':' . config("laravel-hb.servisAnahtari")),
            'user-agent' => config("laravel-hb.username")
            ]
        ];

        $this->_client  = new Client($this->_parameters);

    }


}

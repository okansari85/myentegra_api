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

        if (is_null(config("laravel-hb.username")) || is_null(config("laravel-hb.password" )) | is_null(config("laravel-hb.merchantId" ))) {
            {
                throw new N11Exception("Some Credientals Not Provideds");
            }
        }

        $this->_merchantID = config("laravel-hb.merchantId" );

        $this->_parameters = [
            'headers' => [
            'accept' => 'application/json',
            'authorization' => 'Basic ' .  base64_encode(config("laravel-hb.username") . ':' . config("laravel-hb.password" )),
            ]
        ];

        $this->_client  = new Client($this->_parameters);

    }


}

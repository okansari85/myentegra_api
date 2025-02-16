<?php

namespace App\Services;
use App\Exceptions\BotException;
use GuzzleHttp\Client;
use App\Models\UserAgent;


class BotService
{
    protected $_parameters = null;
    protected $_client;

    public function __construct()
    {

        $randomUserAgent = UserAgent::inRandomOrder()->first()->user_agent;

        // Seçilen user-agent'ı headers'a ekleme
        $this->_parameters = [
            'headers' => [
                'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
                'user-agent' => $randomUserAgent,
            ]
        ];



        $this->_client  = new Client($this->_parameters);

    }


}

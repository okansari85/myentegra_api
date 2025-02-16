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


        $this->_parameters = [
            'headers' => [
                'User-Agent' => $randomUserAgent,
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Encoding' => 'gzip, deflate, br',
                'Accept-Language' => 'tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
                'Referer' => 'https://www.google.com/', // Kaynak siteyi Google gibi gösteriyoruz
                'Connection' => 'keep-alive',
                'Upgrade-Insecure-Requests' => '1',
                'Cache-Control' => 'max-age=0',
            ],
            'cookies' => true, // Çerezleri aktif eder
            'verify' => false, // SSL hatalarına karşı devre dışı bırakır (Güvenlik riskli olabilir)
        ];



        $this->_client  = new Client($this->_parameters);

    }


}

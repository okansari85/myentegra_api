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
                ':authority' => 'ilacfiyati.com',
                ':method' => 'GET',
                ':path' => '/ilaclar?pg=1',
                ':scheme' => 'https',
                'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
                'accept-encoding' => 'gzip, deflate, br, zstd',
                'accept-language' => 'tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
                'cache-control' => 'max-age=0',
                'cookie' => 'PHPSESSID=ga69skrpgqq23u77s4e6eh4fgc; _gid=GA1.2.1543150908.1739691752; _ga_SP1HJH51TT=GS1.1.1739718722.2.1.1739726366.0.0.0; _ga=GA1.2.1118843062.1739691751; _gat_gtag_UA_139598330_1=1',
                'dnt' => '1',
                'priority' => 'u=0, i',
                'referer' => 'https://ilacfiyati.com/ilaclar?pg=1',
                'sec-ch-ua' => '"Not A(Brand";v="8", "Chromium";v="132", "Google Chrome";v="132"',
                'sec-ch-ua-mobile' => '?0',
                'sec-ch-ua-platform' => '"Windows"',
                'sec-fetch-dest' => 'document',
                'sec-fetch-mode' => 'navigate',
                'sec-fetch-site' => 'same-origin',
                'sec-fetch-user' => '?1',
                'upgrade-insecure-requests' => '1',
                'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36',
            ],
            'cookies' => new \GuzzleHttp\Cookie\CookieJar(), // Cookie desteği aktif et
            'allow_redirects' => true, // Yönlendirmeleri takip et
            'verify' => false, // SSL hatalarını yok say (Opsiyonel)
        ];


        $this->_client  = new Client($this->_parameters);

    }


}

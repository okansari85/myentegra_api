<?php

namespace App\Services;
use App\Exceptions\PazaramaException;

use GuzzleHttp\Client;

use App\Models\PazaramaToken;

class PazaramaService
{
    protected $_parameters = null;
    protected $_client;
    protected $_saticiID;
    protected $_apiKey;
    protected $_apiSecret;
    protected $_authUrl;
    protected $_baseUri;


    public function __construct()
    {
        $this->_baseUri = 'https://isortagimapi.pazarama.com';
        $this->_authUrl = 'https://isortagimgiris.pazarama.com';



        $saticiId = config("laravel-pazarama.saticiId");
        $apiKey = config("laravel-pazarama.apiKey");
        $apiSecret = config("laravel-pazarama.apiSecret");


        if (is_null($saticiId) || is_null($apiKey) || is_null($apiSecret)) {
            throw new PazaramaException("Some Credentials Not Provided");
        }

        $this->_saticiID = $saticiId;
        $this->_apiKey = $apiKey;
        $this->_apiSecret = $apiSecret;



        $options = [
            'base_uri' => $this->_authUrl,
            'headers' => [
            'accept' => 'application/json',
            ]
        ];

        $this->_client  = new Client($options);

    }




    protected function setAccessToken()
    {


        $response = $this->_client->post('/connect/token', [
            'headers' => [
                'authorization' => 'Basic ' .  base64_encode($this->_apiKey . ':' .  $this->_apiSecret),
            ],
            'form_params' => [
                'grant_type' => 'client_credentials',
                'scope' => 'merchantgatewayapi.fullaccess',
            ],
        ]);


        if ($response->getStatusCode() !== 200) {
            throw new PazaramaException('Failed to retrieve token');
        }

        $res = json_decode($response->getBody()->getContents(), true);
        $data= $res['data'];

        PazaramaToken::updateOrCreate(
            [
                'token' => $data['accessToken'],
            ],
            [
                'token' => $data['accessToken'],
                'expires_at' => now()->addSeconds($data['expiresIn'] - 60),
            ]
        );
    }



    protected function getAccessToken()
    {
        $token = PazaramaToken::latest()->first();
        if ($token && $token->expires_at > now()) {
            return $token->token;
        }
        return $this->refreshAccessToken();
    }

    protected function refreshAccessToken()
    {
        $this->setAccessToken();
        return $this->getAccessToken();
    }


    protected function setEndPoint(string $endPoint)
    {
        $this->_parameters = [
            'base_uri' => rtrim($this->_baseUri, '/') . '/' . ltrim($endPoint, '/'), // URL birleşimini doğrula
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->getAccessToken(),
            ]
        ];

        return new Client($this->_parameters);
    }





}

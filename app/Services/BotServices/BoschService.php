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

    public function getProduct($productID = null){
        try {

            $url = self::END_POINT . ($productID ? "/$productID" : '');

            // GET isteğini yap
            $response = $this->_client->request('GET', $url);

            // Yanıtın içeriğini JSON olarak döndür
            return $response->getBody()->getContents();

            // Yanıtın JSON formatında olduğunu varsayıyoruz
        } catch (RequestException $e) {
            // Guzzle ile ilgili bir hata oluştu
            throw new BotException("Guzzle hatası: " . $e->getMessage(), $e->getCode(), $e);
        } catch (\Exception $e) {
            // Diğer genel hatalar
            throw new BotException("Veri çekilemedi: " . $e->getMessage(), $e->getCode(), $e);
        }

    }

}

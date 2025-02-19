<?php

namespace App\Services\BotServices;

use App\Exceptions\BotException;
use App\Services\BotService;
use App\Interfaces\IBotApi\IIlacFiyati;
use Illuminate\Support\Arr;


class IlacFiyatiService extends BotService implements IIlacFiyati
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getMedicines($page = null){
        try {

            // Sayfa parametresi varsa URL'ye ekleyin, yoksa 'kuehlschrank' kısmını kullanın
            $url = self::END_POINT . '/ilaclar' . ($page ? "?pg=$page" : '');

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

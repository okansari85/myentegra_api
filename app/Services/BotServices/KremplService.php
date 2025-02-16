<?php

namespace App\Services\BotServices;

use App\Exceptions\BotException;
use App\Services\BotService;
use App\Interfaces\IBotApi\IKrempl;
use Illuminate\Support\Arr;


class KremplService extends BotService implements IKrempl
{

    public function __construct(){
        parent::__construct();
    }

    public function getRandomProxy($page = 1){
        try {
            $url = "https://proxylist.geonode.com/api/proxy-list?protocols=http&limit=500&page=1&sort_by=lastChecked&sort_type=desc";

            // GET isteğini yap
            $response = $this->_client->request('GET', $url);

            // Yanıtın içeriğini JSON olarak döndür ve PHP dizisine dönüştür
            $data = json_decode($response->getBody()->getContents(), true);

            // Proxy listesi dizisini al
            $proxies = $data['data'] ?? [];

            if (empty($proxies)) {
                throw new \Exception("Proxy listesi boş.");
            }

            // Rastgele bir proxy seç
            $randomProxy = $proxies[array_rand($proxies)];

            // Proxy bilgilerini döndür
            return $randomProxy;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            // Guzzle ile ilgili bir hata oluştu
            throw new BotException("Guzzle hatası: " . $e->getMessage(), $e->getCode(), $e);
        } catch (\Exception $e) {
            // Diğer genel hatalar
            throw new BotException("Veri çekilemedi: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getFridges($page = null){
        try {

            $proxy = $this->getRandomProxy(); // Rastgele bir proxy al

            // Proxy ayarlarını belirleyin
            $options = [
                'proxy' => $proxy['protocols'][0] . '://' . $proxy['ip'] . ':' . $proxy['port'],
            ];


            // Sayfa parametresi varsa URL'ye ekleyin, yoksa 'kuehlschrank' kısmını kullanın
            $url = self::END_POINT . '/kuehlschrank' . ($page ? "/$page" : '');

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

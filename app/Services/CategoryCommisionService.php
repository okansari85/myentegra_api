<?php

namespace App\Services;

use App\Interfaces\ICategoryCommision;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use DOMDocument;
use Carbon\Carbon;
use App\Models\N11CategoryCommission;
use Illuminate\Support\Facades\Storage;



class CategoryCommisionService implements ICategoryCommision
{
        public function getN11CategoryCommisions(){

            $client = new Client([
                'headers' => [
                    'User-Agent' => $_SERVER['HTTP_USER_AGENT'],
                    'Accept-Charset' => 'utf-8'
                ],
                'decode_content' => 'utf-8',
            ]);


            try {
                // CURL isteğini gönderme
                $response = $client->request('GET', 'https://magazadestek.n11.com/s/komisyon-oranlari' );

                // Yanıtı alma ve işleme
                $statusCode = $response->getStatusCode();
                //$html = (string)$response->getBody(true)->getContents();
                $html=mb_convert_encoding($response->getBody(true)->getContents(), 'HTML-ENTITIES', 'UTF-8');


                $dom = new DOMDocument();
                libxml_use_internal_errors(true);
                $dom->validateOnParse = true;
                $dom->loadHTML($html);

                $tables = $dom->getElementsByTagName('table')->item(0);


                $data = [];
                $timestamp = Carbon::now();

                $trs = $tables->getElementsByTagName('tr');
                $data = [];

                //sql den al kategorileri arraya at




                foreach($trs as $tr){

                    if ($tr->getElementsByTagName('td')->length){
                        $cat4 = $tr->getElementsByTagName('td')->item(0)->nodeValue;
                        $cat3 = $tr->getElementsByTagName('td')->item(1)->nodeValue;
                        $cat2 = $tr->getElementsByTagName('td')->item(2)->nodeValue;
                        $cat1 = $tr->getElementsByTagName('td')->item(3)->nodeValue;

                        //kategori node oluştur
                        $combined = $this->createCategoryNode($cat4,$cat3,$cat2,$cat1);


                    }

                }




                //return response()->json(['status' => 'success', 'data' => $html]);

            } catch (\Exception $e) {
                // Hata durumunda işlemler
                return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
            }

        }
        
        public function createCategoryNode($cat4,$cat3,$cat2,$cat1){

            $combined = $cat4;
            if ($cat4 !== $cat3) {
                $combined .= " > " . $cat3;
            }
            if ($cat3 !== $cat2) {
                $combined .= " > " . $cat2;
            }
            if ($cat2 !== $cat1) {
                $combined .= " > " . $cat1;
            }
            return $combined;
        }


}

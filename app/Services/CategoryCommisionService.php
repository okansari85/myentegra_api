<?php

namespace App\Services;

use App\Interfaces\ICategoryCommision;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use DOMDocument;
use Carbon\Carbon;
use App\Models\N11CategoryCommission;
use App\Models\N11CategoryIds;
use Illuminate\Support\Facades\Storage;

use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\ClientException;

use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Support\Facades\Http;

class CategoryCommisionService implements ICategoryCommision
{


        public function getN11CategoryCommisionsFromN11(){

            $client = new Client([
                'cookies' => true,
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36',
                    'Accept-Charset' => 'utf-8',
                    'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9'
                ],
                'decode_content' => 'utf-8',
            ]);









            try {
                // CURL isteğini gönderme
                $response = $client->request('GET', 'https://magazadestek.n11.com/s/komisyon-oranlari');
                //$response = Http::get('https://magazadestek.n11.com/s/komisyon-oranlari');
                // Yanıtı alma ve işleme
                $statusCode = $response->getStatusCode();
                $html = (string)$response->getBody(true)->getContents();
                $html=mb_convert_encoding($response->getBody(true)->getContents(), 'HTML-ENTITIES', 'UTF-8');




                $html=mb_convert_encoding($output,'HTML-ENTITIES', 'UTF-8');

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
                        $komsiyon_orani = $tr->getElementsByTagName('td')->item(4)->nodeValue;
                        $pazarlama_hizmet_orani = $this->format_and_add_kdv($tr->getElementsByTagName('td')->item(5)->nodeValue);
                        $pazaryeri_hizmet_orani = $this->format_and_add_kdv($tr->getElementsByTagName('td')->item(6)->nodeValue);


                    $data[] = [
                        'category_name' => $combined,
                        'komsiyon_orani' => number_format((float)$komsiyon_orani, 2, '.', ''),
                        'pazarlama_hizmet_orani' => $pazarlama_hizmet_orani,
                        'pazaryeri_hizmet_orani' => $pazaryeri_hizmet_orani,
                    ];

                    }

                }

                N11CategoryCommission::truncate();
                N11CategoryCommission::insert($data);


                $categories = DB::table('n11_category_commision')
                ->leftJoin('n11_category_ids', 'n11_category_ids.name', '=', 'n11_category_commision.category_name')
                ->select("n11_category_commision.*","n11_category_ids.n11_category_id")
                ->get();

                return response()->json(['status' => 'success'],200);

            } catch (\Exception $e) {
                // Hata durumunda işlemler
                return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
            }

        }

        public function getN11CommissionRates($search,$per_page){

            return response()->json(DB::table('n11_category_commision')
            ->leftJoin('n11_category_ids', 'n11_category_ids.name', '=', 'n11_category_commision.category_name')
            ->select("n11_category_commision.*","n11_category_ids.n11_category_id")
            ->where(function ($query) use ($search) {
                $query->where(DB::raw('lower(n11_category_commision.category_name)'), 'like', '%' . mb_strtolower($search) . '%');
            })
           ->orderBy('id','desc')
           ->paginate($per_page)
           ->appends(request()->query()),200);

        }

        private function format_and_add_kdv($str){
            //yazıyı sil
            $str = str_replace("%","",str_replace(" + KDV","",$str));
            //rakama çevir
            $str = number_format((float)$str, 2, '.', '') * 1.2;
            return number_format((float)$str, 2, '.', '');
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

        public function getN11CategoryCommissionByCategoryId($n11_category_id){

            $ko = N11CategoryIds::leftJoin('n11_category_commision', function($join) {
                $join->on('n11_category_ids.name', '=', 'n11_category_commision.category_name');
              })
              ->where ('n11_category_ids.n11_category_id', "=", $n11_category_id)
              ->first([
                'n11_category_commision.komsiyon_orani',
                'n11_category_commision.pazarlama_hizmet_orani',
                'n11_category_commision.pazaryeri_hizmet_orani',
              ]);

            $toplam_komisyon = number_format((float)$ko->komsiyon_orani, 2, '.', '') +
                               number_format((float)$ko->pazarlama_hizmet_orani , 2, '.', '')+
                               number_format((float)$ko->pazaryeri_hizmet_orani, 2, '.', '');


            return $toplam_komisyon;


        }




}

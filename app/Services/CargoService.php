<?php

namespace App\Services;

use App\Interfaces\ICargo;
use GuzzleHttp\Client;
use DOMDocument;
use Illuminate\Support\Facades\DB;
use App\Models\N11CargoPrices;
use Carbon\Carbon;
use App\Models\HBCargoPrices;
use App\Imports\HBCargoPriceImport;
use Maatwebsite\Excel\Facades\Excel;


class CargoService implements ICargo
{
    public function getN11CargoPrices(){

        $n11CargoPrices = N11CargoPrices::all();
        return response()->json($n11CargoPrices,200);

    }

    public function getCargoPricesFromN11(){

        $client = new Client([
            'headers' => [
                'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.104 Safari/537.36',
                'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
                'origin' => 'https://www.n11.com',
                'accept-encoding: gzip, deflate, br',
            ]
            ]);


        try {
            // CURL isteğini gönderme
            $response = $client->request('GET', 'https://www.n11.com/kampanyalar/ozel-kargo-kampanyasi');

            print_r($response);
            return;
            // Yanıtı alma ve işleme
            $statusCode = $response->getStatusCode();
            $html = (string)$response->getBody(true)->getContents();



            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $dom->validateOnParse = true;
            $dom->loadHTML($html);

            $tables = $dom->getElementsByTagName('table')->item(0);
            $trs = $tables->getElementsByTagName('tr');

            $data = [];
            $timestamp = Carbon::now();

            foreach($trs as $tr){

                if ($tr->getElementsByTagName('td')->length) {
                    $desi = ($tr->getElementsByTagName('td')->item(0)->nodeValue) == "Dosya" ? 0 : $tr->getElementsByTagName('td')->item(0)->nodeValue;
                    $yk_fiyat = str_replace(",",".",$tr->getElementsByTagName('td')->item(4)->nodeValue);
                    $aras_price = str_replace(",",".",$tr->getElementsByTagName('td')->item(1)->nodeValue);
                    $ptt_price = str_replace(",",".",$tr->getElementsByTagName('td')->item(2)->nodeValue);
                    $mng_price = str_replace(",",".",$tr->getElementsByTagName('td')->item(3)->nodeValue);
                    $surat_price = str_replace(",",".",$tr->getElementsByTagName('td')->item(5)->nodeValue);
                    $sendeo_price = str_replace(",",".",$tr->getElementsByTagName('td')->item(6)->nodeValue);

                    $data[] = [
                        'desi' => $desi,
                        'yk_price' => number_format((float)$yk_fiyat, 2, '.', ''),
                        'aras_price' => number_format((float)$aras_price, 2, '.', ''),
                        'ptt_price' => number_format((float)$ptt_price, 2, '.', ''),
                        'mng_price' => number_format((float)$mng_price, 2, '.', ''),
                        'surat_price' => number_format((float)$surat_price, 2, '.', ''),
                        'sendeo_price' => number_format((float)$sendeo_price, 2, '.', ''),
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ];
                }
            }
            N11CargoPrices::truncate();
            N11CargoPrices::insert($data);

            return response()->json(['status' => 'success', 'data' => $data]);

        } catch (\Exception $e) {
            // Hata durumunda işlemler
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }

    }

    public function importHbCargoPricesFromFile($file){

        HBCargoPrices::truncate();
        Excel::import(new HBCargoPriceImport,$file);
        return response()->json("ok",200);

    }

    public function getCargoPriceByDesi($desi){
        return N11CargoPrices::where('desi',"=",$desi)->pluck('yk_price')->first();
    }




}

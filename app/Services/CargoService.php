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

    public function getCargoPricesFromN11(){

        $client = new Client([
            'headers' => ['content-type' => 'application/json', 'Accept' => 'applicatipon/json', 'charset' => 'utf-8']
            ]);


        try {
            // CURL isteğini gönderme
            $response = $client->request('GET', 'https://www.n11.com/kampanyalar/ozel-kargo-kampanyasi');

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
                    $fiyat = str_replace(",",".",$tr->getElementsByTagName('td')->item(4)->nodeValue);

                    $data[] = [
                        'desi' => $desi,
                        'yk_price' => number_format((float)$fiyat, 2, '.', ''),
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




}

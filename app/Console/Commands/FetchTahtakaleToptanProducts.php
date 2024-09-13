<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use SimpleXMLElement;
use App\Jobs\ImportTahtakaleToptanProducts;
use App\Models\Products;

class FetchTahtakaleToptanProducts extends Command
{
    protected $signature = 'fetch:tahtakale-toptan-products';
    protected $description = 'Fetch products from Tahtakale Toptan and dispatch the job';

    public function handle()
    {
        $this->info('Tahtakale Toptan ürünleri çekiliyor');
        $batch = Bus::batch([])->name('importtahtakeleproducts')->dispatch();

        $response = Http::get('https://www.tahtakaletoptanticaret.com/export.xml');

        if ($response->failed()) {
            \Log::error('Failed to fetch XML file');
            return;
        }

        // Geçici dosya oluştur
        $tempFilePath = storage_path('app/temp_products.xml');
        file_put_contents($tempFilePath, $response->body());

        $reader = new \XMLReader();
        $reader->open($tempFilePath);

        $productinXml = [];

        while ($reader->read()) {
            if ($reader->nodeType == \XMLReader::ELEMENT && $reader->name == 'item') {
                $productXml = $reader->readOuterXML();
                $product = new SimpleXMLElement($productXml);

                $productData = [
                    'barcode' => (string) $product->barcode,
                    'title' => (string) $product->title,
                    'price' => (float) $product->price,
                    'quantity' => (int) $product->quantity,
                    'description' => (string) $product->description,
                    'category' => (string) $product->category,
                    'image_link' => (string) $product->image_link,
                ];

                // Ek resimleri al
                $i = 1;
                while (isset($product->{'additional_image_link' . $i})) {
                    $productData['additional_image_link' . $i] = (string) $product->{'additional_image_link' . $i};
                    $i++;
                }

               $batch->add(new ImportTahtakaleToptanProducts($productData));
               $productinXml[]=(string) $product->barcode;
            }
        }

        // XML'de olmayan ürünleri pasif yap
        Products::whereNotIn('productCode', $productinXml)
        ->where('supplier_id', 2)
        ->update(['stock' => 0]);


        $this->info('Tahtakale Toptan ürünleri çekildi');
    }
}

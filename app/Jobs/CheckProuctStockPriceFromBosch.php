<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Batchable;

use App\Interfaces\IBotApi\IBosch;
use DOMDocument;
use Carbon\Carbon;

use App\Models\Products;

class CheckProuctStockPriceFromBosch implements ShouldQueue
{
    use Batchable,Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $product;
    public $tries = 3;
    private IBosch $boschService;

    public function __construct($_product){
        $this->product = $_product;
    }


    public function handle(IBosch $boschService): void
    {
        //
        $response = $boschService->getProduct($this->product->productCode);

        if (empty($response)) {
            throw new \Exception("Boş yanıt alındı.");
        }


        $dom = new DOMDocument();
        libxml_use_internal_errors(true);

        $dom->validateOnParse = true;
        $dom->loadHTML($response);

        $xpath = new \DOMXPath($dom);

        // Fiyat bilgisini çek
        $priceNode = $xpath->query("//div[@data-testid='price-main-price']");
        $price = $priceNode->length > 0 ? trim($priceNode->item(0)->nodeValue) : null;
        $price = str_replace(['₺', ','], ['', '.'], $price);
        $price = (float)$price;

        // Stok durumunu kontrol et
        $stockNode = $xpath->query("//div[@data-testid='price-availability']");
        $stockText = $stockNode->length > 0 ? trim($stockNode->item(0)->nodeValue) : null;

        $stock = str_contains($stockText, 'Stokta mevcut') ? '500' : '0';
        $stock = (float)$stock;

        $product = Products::findOrFail($this->product->id);

        $product->update([
            'stock' => $stock,
            'price' => $price,
        ]);

    }
}

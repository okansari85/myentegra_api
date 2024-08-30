<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Batchable;

use Carbon\Carbon;
use App\Enum\OrderStatusEnum;

use App\Interfaces\IPazaramaApi\IProduct;

use App\Models\PazaramaProduct;
use App\Models\PazaramaProductImage;
use App\Models\PazaramaProductAttribute;

use Illuminate\Support\Facades\DB; // Transaction kullanabilmek için gerekli


class AddProductToPazaramaProductByCode implements ShouldQueue
{
    use Batchable,Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $order;
    public $tries = 3;

    public function __construct($_order)
    {
        $this->order = $_order;
    }

    public function handle(IProduct $pazaramaProductService): void
    {

        $order_items = $this->order['items'];

        foreach ($order_items as $item){
            $productCode = $item['product']['code'];
            $this->getPazaramaProduct($productCode,$pazaramaProductService);
        }

    }

    public function getPazaramaProduct($productCode, IProduct $pazaramaProductService)
    {
        if (!PazaramaProduct::where('code', $productCode)->exists()) {
            $data = ['Code' => $productCode];
            $pazaramaProduct = $pazaramaProductService->getProductByCode($data);
            $this->addPazaramaProduct($pazaramaProduct['data']);
        }
    }


public function addPazaramaProduct(array $productData)
{
    DB::beginTransaction(); // Transaction başlat

    try {
        // Ürünü oluştur veya mevcut olanı getir
        $product = PazaramaProduct::firstOrCreate(
            [
                'code' => $productData['code']
            ], // Benzersiz alan veya koşul
            [
                'name' => $productData['name'] ?? '',
                'displayName' => $productData['displayName'] ?? '',
                'description' => $productData['description'] ?? '',
                'brandId' => $productData['brandId'] ?? '',
                'brandName' => $productData['brandName'] ?? '',
                'stockCount' => $productData['stockCount'] ?? 0,
                'stockCode' => $productData['stockCode'] ?? '',
                'priorityRank' => $productData['priorityRank'] ?? 0,
                'vatRate' => $productData['vatRate'] ?? 0.00,
                'listPrice' => $productData['listPrice'] ?? 0.00,
                'salePrice' => $productData['salePrice'] ?? 0.00,
                'installmentCount' => $productData['installmentCount'] ?? 0,
                'categoryId' => $productData['categoryId'] ?? '',
                'state' => $productData['state'] ?? 0,
                'stateDescription' => $productData['stateDescription'] ?? '',
                'isCatalogProduct' => $productData['isCatalogProduct'] ?? 0,
                'groupCode' => $productData['groupCode'] ?? '',
            ]
        );

        // Ürünün ID'sinin dolu olduğundan emin olun
        if (!$product->id) {
            throw new \Exception('Product ID not found after creation.');
        }

        // İlişkili ürün özelliklerini ekleyin
        if (isset($productData['attributes']) && is_array($productData['attributes'])) {
            foreach ($productData['attributes'] as $attribute) {
                PazaramaProductAttribute::create([
                    'pazarama_product_id' => $product->id, // Doğru şekilde ilişkilendirildi
                    'attributeId' => $attribute['attributeId'] ?? '',
                    'attributeValueId' => $attribute['attributeValueId'] ?? '',
                ]);
            }
        }

        // İlişkili ürün görsellerini ekleyin
        if (isset($productData['images']) && is_array($productData['images'])) {
            foreach ($productData['images'] as $image) {
                PazaramaProductImage::create([
                    'pazarama_product_id' => $product->id, // Doğru şekilde ilişkilendirildi
                    'imageUrl' => $image['imageUrl'] ?? '',
                ]);
            }
        }


            DB::commit(); // Transaction'ı onayla
        } catch (\Exception $e) {
            DB::rollBack(); // Hata durumunda transaction'ı geri al
            // Hatanın günlüğe kaydedilmesi veya kullanıcıya bildirilmesi
            throw $e; // veya uygun bir hata mesajı dönebilirsiniz
        }
    }



}

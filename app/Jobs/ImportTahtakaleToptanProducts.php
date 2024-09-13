<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\Products;
use App\Models\ProductImages;
use App\Models\ProductCategory; // Category modelini ekleyin
use Illuminate\Support\Facades\DB;
use SimpleXMLElement;

use Illuminate\Bus\Batchable;

class ImportTahtakaleToptanProducts implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $productData;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $productData)
    {
        $this->productData = $productData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $product = (object) $this->productData;

        $productCode = (string) $product->barcode;
        $existingProduct = Products::where('productCode', $productCode)->first();

        $category_id = $this->findOrCreateCategoryHierarchy((string) $product->category);

        $data = [
            'productCode' => $productCode,
            'productTitle' => (string) $product->title,
            'price' => (float) $product->price,
            //'desi' => 0,
            'stock' => (int) $product->quantity,
            'profit_rate' => 4.50,
            'description' => (string) $product->description,
            'category_id' => $category_id ? $category_id->id : null,
            'supplier_id' => 2
        ];

        if ($existingProduct) {
            $existingProduct->update($data);
        } else {
            $existingProduct = Products::create($data);
        }

        // Ana resim ve ek resimleri işleme
        $imageLinks = [];

        // Ana resim
        if (!empty($product->image_link)) {
            $primaryImageUrl = (string) $product->image_link;
            $imageLinks[] = [
                'url' => $primaryImageUrl,
                'name' => 'Primary Image',
                'type' => 'primary',
                'order' => 1
            ];
        }

        // Ek resimler
        $i = 1;
        while (isset($product->{'additional_image_link' . $i})) {
            $additionalImageUrl = (string) $product->{'additional_image_link' . $i};
            $imageLinks[] = [
                'url' => $additionalImageUrl,
                'name' => 'Additional Image ' . $i,
                'type' => 'additional',
                'order' => $i + 1
            ];
            $i++;
        }

        // Önce mevcut resimleri sil (isteğe bağlı)
        $existingProduct->images()->delete();

        // Yeni resimleri ekle
        foreach ($imageLinks as $image) {
            ProductImages::create([
                'product_id' => $existingProduct->id,
                'name' => $image['name'],
                'type' => $image['type'],
                'order' => $image['order'],
                'url' => $image['url'],
            ]);
        }
    }

    /**
     * Kategori hiyerarşisini bul veya oluştur
     *
     * @param string $categoryPath
     * @return Category|null
     */
    protected function findOrCreateCategoryHierarchy($categoryPath)
    {
        if (empty($categoryPath)) {
            return null;
        }

        // Kategori hiyerarşisini ayrıştır
        $categories = array_map('trim', explode('&gt;', $categoryPath));
        $parent_id = 0; // Başlangıçta kök kategori

        $lastCategory = null;

        foreach ($categories as $categoryName) {
            // Mevcut kategoriyi kontrol et veya oluştur
            $category = ProductCategory::firstOrCreate(
                ['name' => $categoryName, 'parent_id' => $parent_id],
                ['order' => 0] // Varsayılan sıralama
            );

            // Mevcut kategorinin ID'sini parent_id olarak ayarla
            $parent_id = $category->id;
            $lastCategory = $category;
        }

        return $lastCategory;
    }
}

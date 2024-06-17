<?php

namespace App\Services;

use App\Interfaces\IMalzemos;
use App\Models\Malzemos;
use App\Models\StokHareketleri;
use Illuminate\Support\Facades\DB;

class MalzemosService implements IMalzemos
{
    public function getMalzemos($search=null,$per_page=null,$depo_id=null){

        return response()->json(Malzemos::with('raf.descendants')
        ->select('id','productDesc', 'depo_id','raf_id','productCode','stock', 'created_at', 'updated_at')
        ->where(function ($query) use ($search) {
              $query->where(DB::raw('lower(productCode)'), 'like', '%' . mb_strtolower($search) . '%');
         })
         ->when($depo_id != 0, function ($query) use ($depo_id) {
              $query->where('depo_id', '=', $depo_id);
        })
        ->orderBy('id','desc')
        ->paginate($per_page)
        ->appends(request()->query()),200);

    }

    public function getMalzemosByProductCode($productCode,$depoId){

        return Malzemos::where('productCode', $productCode)
                       ->where('depo_id', $depoId)
                       ->first(); // Tek bir kayıt döndürür


    }

    public function addProductToStock(int $product_id, int $quantity)
    {
        // İşlemi atomik yapmak için bir veritabanı işlemi (transaction) başlat
        DB::beginTransaction();

        try {
            // Ürünü ID'ye göre bul
            $product = Malzemos::findOrFail($product_id);


            // Ürünün stok miktarını arttır
            $product->stock += $quantity;

            // Güncellenmiş ürünü kaydet
            $product->save();


            $stockMovement = new StokHareketleri();
            $stockMovement->product_id = $product_id;
            $stockMovement->stock = $quantity;
            $stockMovement->type = 'IN';  // Stok girişini belirtir
            $stockMovement->save();

            // İşlemi başarılı bir şekilde bitir
            DB::commit();

            return $stockMovement->load('malzemos.raf.descendants');

        } catch (\Exception $e) {
            // Bir hata oluşursa işlemi geri al (rollback)
            DB::rollBack();

            // Hatayı üst seviyeye fırlat
            throw new \Exception("Stoğa ürün eklenirken bir hata oluştu: " . $e->getMessage());
        }
    }



    public function deleteStockMovement(int $stockMovementId)
    {
        // İşlemi atomik yapmak için bir veritabanı işlemi (transaction) başlat
        DB::beginTransaction();

        try {
            // Stok hareketini ID'ye göre bul
            $stockMovement = StokHareketleri::findOrFail($stockMovementId);

            // Stok miktarını güncellemek için ilgili ürünü bul
            $product = $stockMovement->malzemos;

            // Eğer hareket 'IN' ise stok miktarını azalt, 'OUT' ise arttır
            if ($stockMovement->type === 'IN') {
                $product->stock -= $stockMovement->stock;
            } else if ($stockMovement->type === 'OUT') {
                $product->stock += $stockMovement->stock;
            }

            // Güncellenmiş ürünü kaydet
            $product->save();

            // Stok hareketini sil
            $stockMovement->delete();

            // İşlemi başarılı bir şekilde bitir
            DB::commit();

            return true;
        } catch (\Exception $e) {
            // Bir hata oluşursa işlemi geri al (rollback)
            DB::rollBack();

            // Hatayı üst seviyeye fırlat
            throw new \Exception("Stok hareketi silinirken bir hata oluştu: " . $e->getMessage());
        }
    }


    public function removeProductFromStock(int $product_id, int $quantity)
    {
        // İşlemi atomik yapmak için bir veritabanı işlemi (transaction) başlat
        DB::beginTransaction();

        try {
            // Ürünü ID'ye göre bul
            $product = Malzemos::findOrFail($product_id);

            // Eğer stok miktarı yetersizse hata fırlat
            if ($product->stock < $quantity) {
                throw new \Exception("Yetersiz stok miktarı.");
            }

            // Ürünün stok miktarını azalt
            $product->stock -= $quantity;

            // Güncellenmiş ürünü kaydet
            $product->save();

            // Stok hareketini kaydet
            $stockMovement = new StokHareketleri();
            $stockMovement->product_id = $product_id;
            $stockMovement->stock = $quantity;
            $stockMovement->type = 'OUT';  // Stok çıkışını belirtir
            $stockMovement->save();

            // İşlemi başarılı bir şekilde bitir
            DB::commit();

            // Stok hareketini ilişkili ürün ile birlikte döndür
            return $stockMovement->load('malzemos.raf.descendants');

        } catch (\Exception $e) {
            // Bir hata oluşursa işlemi geri al (rollback)
            DB::rollBack();

            // Hatayı üst seviyeye fırlat
            throw new \Exception("Stoktan ürün çıkarılırken bir hata oluştu: " . $e->getMessage());
        }
    }


    public function saveProduct($productData)
    {
        // Veritabanı işlemi başlat
        DB::beginTransaction();

        try {

            $product = Malzemos::firstOrCreate(
                ['productCode' => $productData['product_code']], // Arama kriteri
                [                                               // Bulunamadığında eklenecek veri
                    'productDesc' => $productData['product_desc'],
                    'stock' => $productData['stock'],
                    'raf_id' => $productData['raf_id'],
                    'depo_id' => $productData['depo_id']
                ]
            );


            // İşlemi başarılı bir şekilde tamamla
            DB::commit();

            // Kaydedilen ürünü döndür
            return $product;
        } catch (\Exception $e) {
            // Bir hata oluşursa işlemi geri al
            DB::rollBack();

            // Hata mesajını üst seviyeye fırlat
            throw new \Exception("Ürün kaydedilirken bir hata oluştu: " . $e->getMessage());
        }

    }

    public function updateProductById($productId, $updatedData)
            {
                try {
                    // Ürünü güncelle
                    $product = Malzemos::findOrFail($productId);

                    // Güncellenecek verileri ata
                    $product->update([
                        'productCode' => $updatedData['product_code'] ?? $product->productCode,
                        'productDesc' => $updatedData['product_desc'] ?? $product->productDesc,
                        'stock' => $updatedData['stock'] ?? $product->stock,
                        'raf_id' => $updatedData['raf_id'] ?? $product->raf_id,
                        'depo_id' => $updatedData['depo_id'] ?? $product->depo_id,
                    ]);

                    // Güncellenen ürünü geri döndür
                    return $product;
                } catch (\Exception $e) {
                    // Hata durumunda istisna fırlat
                    throw new \Exception("Ürün güncellenirken bir hata oluştu: " . $e->getMessage());
                }
            }


        public function deleteProductById($productId)
        {
            DB::beginTransaction();

            try {
                // Ürünü bul
                $product = Malzemos::findOrFail($productId);

                // Ürüne ait stok hareketlerini sil
                StokHareketleri::where('product_id', $productId)->delete();

                // Ürünü sil
                $product->delete();

                // İşlemi başarılı bir şekilde bitir
                DB::commit();

                return response()->json(['message' => 'Ürün ve ilgili stok hareketleri başarıyla silindi.'], 200);

            } catch (\Exception $e) {
                // Bir hata oluşursa işlemi geri al (rollback)
                DB::rollBack();

                // Hata mesajını logla
                Log::error("Ürün silinirken bir hata oluştu: " . $e->getMessage());

                return response()->json(['error' => 'Ürün silinirken bir hata oluştu: ' . $e->getMessage()], 500);
            }
        }

}

<?php

namespace App\Services;

use App\Interfaces\IHakedis;
use App\Models\Depos;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\OrderItems;
use App\Models\Hakedis;
use App\Models\Products;
use App\Models\Orders;

use Carbon\Carbon;


class HakedisService implements IHakedis
{

    public function listHakedisByDay(){

        $hakedisler = Hakedis::with('product','order.buyer.adresses')->orderBy('created_at', 'desc')->get();

        // Siparişleri gün bazlı olarak gruplandır
        $groupedHakedis = $hakedisler->groupBy(function($hakedis) {
            return Carbon::parse($hakedis->created_at)->format('Y-m-d');
        })->map(function ($hakedisGroup) {
            // Grup içindeki hakedişlerin toplam tutarını hesapla
            $totalAmount = $hakedisGroup->sum('total_price'); // 'amount' alanı yerine hakediş toplamını tutan alanı kullanın

            return [
                'hakedisler' => $hakedisGroup,
                'totalAmount' => $totalAmount,
            ];
        });

        return $groupedHakedis;
    }


    public function addHakedisItem($order_id)
    {

        // İşlemleri bir transaction içinde gerçekleştir
        return DB::transaction(function () use ($order_id) {
            try {
                // Verilen order_id'ye göre tüm order item'larını al
                $orderItems = OrderItems::where('order_id', $order_id)->get();

                // Eğer orderItems boşsa, hata fırlat
                if ($orderItems->isEmpty()) {
                    throw new \Exception("Order items not found for the given order ID: $order_id");
                }

                // Toplam item sayısını hesapla (OrderItems sayısı)
                $totalQuantity = $orderItems->count();

                // Eğer totalQuantity 0 ise, hata fırlat
                if ($totalQuantity == 0) {
                    throw new \Exception("Total quantity is zero for the given order ID: $order_id");
                }

                // Belirli bir packet_price, örnek olarak 30 TL
                $totalPacketPrice = 30;
                $unitPacketPrice = $totalPacketPrice / $totalQuantity;

                // Her order item için hakediş kaydı oluştur
                foreach ($orderItems as $item) {
                    // Polymorfik ilişkiyi kullanarak quantity bilgisini al
                    $quantity = $item->orderable->quantity;

                    // Product bilgilerini al
                    $product = Products::find($item->product_id);

                    // Eğer product bulunamazsa, hata fırlat
                    if (!$product) {
                        throw new \Exception("Product not found for product ID: {$item->product_id}");
                    }

                    // Total price hesaplaması
                    $price = $product->price;
                    $total_price = ($price * 1.10 * 1.2) * $quantity + $unitPacketPrice;

                    // Hakedis tablosuna yeni kayıt ekle
                    Hakedis::create([
                        'order_id' => $item->order_id,
                        'product_id' => $product->id,
                        'is_confirmed' => false, // Default olarak false, isteğe göre değiştirilebilir
                        'is_paid' => false, // Default olarak false, isteğe göre değiştirilebilir
                        'price' => $price, // Products tablosundan alınan price
                        'quantity' => $quantity, // Polymorfik ilişkiden alınan quantity
                        'packet_price' => $unitPacketPrice, // 30 TL bölü toplam OrderItems sayısı
                        'total_price' => $total_price, // Price * 1.10 * 1.2 + unitPacketPrice hesaplaması
                    ]);
                }

                // Order'ın durumunu güncelle
                $order = Orders::find($order_id);
                if ($order) {
                    $order->status = 2; // Durumunu 2 olarak ayarla
                    $order->save();
                } else {
                    throw new \Exception("Order not found for the given order ID: $order_id");
                }

                // İşlem başarılı ise true döndür
                return true;

            } catch (\Exception $e) {
                // Hata durumunda hatayı logla
                Log::error("Hakediş kaydı oluşturulurken bir hata oluştu: " . $e->getMessage());

                // İstisna (exception) fırlat
                throw $e;
            }
        });
}
}

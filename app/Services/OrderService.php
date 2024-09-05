<?php

namespace App\Services;

use App\Interfaces\IOrder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Bus;


use App\Models\OrderItems;
use App\Models\Orders;
use App\Models\Products;

class OrderService implements IOrder
{
    public function getAllOrders($search,$per_page,$status){

        $status = $status == "1" ? [$status, 5] : [$status];

        $orders = Orders::with([
            'items.orderable',
            'buyer.adresses',
            'items.product.coverImage',
            'items.product.category.descendants'
        ])
        ->where(function ($query) use ($search, $status) {
            $query->where(DB::raw('lower(market_order_number)'), 'like', '%' . mb_strtolower($search) . '%')
                  ->whereIn('status', $status);
        })
        ->orderBy('id', 'desc')
        ->paginate($per_page);

            // `is_checked_count` değerini hesapla ve ekle
        $orders->each(function ($order) {
            // Her item için (orderable->quantity - checked_quantity) farkını hesapla ve bu farkların toplamını `is_checked_count` olarak belirle
            $order->is_checked_count = $order->items->sum(function ($item) {
                $total_quantity = $item->orderable->quantity ?? 0; // orderable->quantity değeri yoksa varsayılan 0
                $checked_quantity = $item->checked_quantity ?? 0; // checked_quantity değeri yoksa varsayılan 0
                return max(0, $total_quantity - $checked_quantity); // Negatif olmaması için max(0, fark) kullan
            });
        });

        return $orders->appends(request()->query());

    }

    public function getConfirmedOrders($search,$per_page,$status){

        $status = [$status,5];

        return Orders::with('items.orderable','buyer.adresses','items.product.coverImage','items.product.category.descendants')
            ->where(function ($query) use ($search,$status) {
            $query->where(DB::raw('lower(market_order_number)'), 'like', '%' . mb_strtolower($search) . '%');
            $query->whereIn('status', $status);
            })
            ->where('is_confirmed',1)
            ->orderBy('id','desc')
            ->paginate($per_page)
            ->appends(request()->query());

    }

    public function confirmItem($item_id,$product_id){

        $response = DB::transaction(function () use ($item_id, $product_id) {
            // OrderItems kaydını alın
            $order_item = OrderItems::find($item_id);

            if ($order_item) {
                // OrderItems kaydından order_id değerini alın
                $order_id = $order_item->order_id;

                // OrderItems kaydını güncelleyin
                $order_item->product_id = $product_id;
                $order_item->is_confirmed = 1;
                $order_item->save();

                // Aynı order_id'ye sahip ve is_confirmed = 0 olan başka kayıtlar olup olmadığını kontrol edin
                $unconfirmedItems = OrderItems::where('order_id', $order_id)
                                              ->where('is_confirmed', 0)
                                              ->count();

                if ($unconfirmedItems === 0) {
                    // Eğer is_confirmed = 0 olan başka kayıt yoksa, Orders tablosunu güncelleyin
                    $order = Orders::find($order_id);
                    if ($order) {
                        $order->is_confirmed = 1;
                        $order->save();
                        // Başarı mesajı döndür
                        return ['status' => 200, 'message' => 'Sipariş öğesi ve sipariş başarıyla onaylandı'];
                    } else {
                        // Sipariş bulunamazsa hata mesajı döndür
                        return ['status' => 404, 'message' => 'Sipariş bulunamadı'];
                    }
                } else {
                    // Diğer öğeler hala beklemede mesajı döndür
                    return ['status' => 200, 'message' => 'Sipariş öğesi onaylandı, ancak diğer öğeler hala beklemede'];
                }
            } else {
                // OrderItems kaydı bulunamazsa hata mesajı döndür
                return ['status' => 404, 'message' => 'Sipariş öğesi bulunamadı'];
            }
        });

        return response()->json(['message' => $response['message']], $response['status']);


    }

    public function markAsPrinted($order_id)
    {
        DB::beginTransaction();

        try {
            // Siparişi ID'ye göre bul
            $order = Orders::findOrFail($order_id);
            $order->is_printed = 1;
            $order->save();

            // İşlemi başarılı bir şekilde bitir
            DB::commit();
            return $order;

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Sipariş bulunamadıysa geri al (rollback)
            DB::rollBack();
            throw new \Exception("Sipariş bulunamadı: " . $e->getMessage());

        } catch (\Exception $e) {
            // Diğer hatalar durumunda işlemi geri al (rollback)
            DB::rollBack();
            throw new \Exception("Bir hata oluştu: " . $e->getMessage());
        }
    }

    public function markAsChecked($product_code){

        DB::beginTransaction();

        try {
            $targetProduct = $this->findProduct($product_code);

            if (!$targetProduct) {
                DB::rollBack();
                return response()->json(['message' => 'Ürün bulunamadı'], 404);
            }

            $orderItem = $this->findOrderItem($targetProduct);
            if (!$orderItem) {
                DB::rollBack();
                return response()->json(['message' => 'Sipariş öğesi bulunamadı'], 404);
            }

            $this->updateOrderItem($orderItem);
            $this->updateOrder($orderItem->order);

            DB::commit();

            return response()->json([
                'message' => 'Öğe işaretlendi',
                'order_id' => $orderItem->order->id,
                'is_checked' => $orderItem->order->is_checked,
                'is_checked_count' =>  $orderItem->order->items->count() - $orderItem->order->items->where('is_checked', 1)->count(),
                'product_id' => $targetProduct->id,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }

    }


    private function findProduct($product_code)
    {
        return Products::where('productCode', $product_code)->first();
    }

    private function findOrderItem($product)
    {
        return OrderItems::whereHas('order', function ($query) {
            $query->where('status', 2)
                  ->where('is_printed', 1)
                  ->where('is_confirmed', 1);
        })->where('product_id', $product->id)
        ->first();
    }

    private function updateOrderItem($orderItem)
    {
        // Polimorfik ilişki üzerinden sipariş miktarını al
            $quantity = $orderItem->orderable->quantity ?? 0; // `quantity` değeri yoksa varsayılan olarak 0 kullan

            // `checked_quantity`'yi artır ve `quantity`'yi aşmamasını sağla
            if ($orderItem->checked_quantity < $quantity) {
                $orderItem->checked_quantity += 1;
            }

            // `checked_quantity` eşit veya büyükse, item'ı kontrol edilmiş olarak işaretle
            $orderItem->is_checked = ($orderItem->checked_quantity == $quantity) ? 1 : 0;

            // Değişiklikleri kaydet
            $orderItem->save();
    }

    private function updateOrder($order)
    {
        $allItemsChecked = $order->items->every(function ($item) {
            return $item->is_checked == 1;
        });

        if ($allItemsChecked) {
            $order->is_checked = 1;
            $order->save();
        }
    }


}

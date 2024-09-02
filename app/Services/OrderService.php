<?php

namespace App\Services;

use App\Interfaces\IOrder;
use App\Models\Orders;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Bus;
use App\Models\OrderItems;

class OrderService implements IOrder
{
    public function getAllOrders($search,$per_page,$status){

        $status = [$status,5];

        return Orders::with('items.orderable','buyer.adresses','items.product.coverImage','items.product.category.descendants')
            ->where(function ($query) use ($search,$status) {
            $query->where(DB::raw('lower(market_order_number)'), 'like', '%' . mb_strtolower($search) . '%');
            $query->whereIn('status', $status);
            })
            ->orderBy('id','desc')
            ->paginate($per_page)
            ->appends(request()->query());

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


}

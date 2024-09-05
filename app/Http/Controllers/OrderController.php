<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Interfaces\IOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Carbon\Carbon;
use Validator;


use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    //

    private IOrder $orderService;

    public function __construct(IOrder $_orderService)
    {
        $this->orderService = $_orderService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $search = $request->query('search');
        $per_page = $request->query('per_page');
        $status = $request->query('status');

        return response()->json($this->orderService->getAllOrders($search,$per_page,$status));
    }

    public function confirmItem(Request $request) {

        $item_id = $request->input('item_id');
        $product_id = $request->input('product_id');

        // ProductService içindeki confirmItem metodunu çağır
        return $this->orderService->confirmItem($item_id, $product_id);
    }

    public function getConfirmedOrders(Request $request){

        $search = $request->query('search');
        $per_page = $request->query('per_page');
        $status = $request->query('status');

        return response()->json($this->orderService->getConfirmedOrders($search,$per_page,$status));
    }

    public function markAsPrinted(Request $request)
    {
        // Validation
        $request->validate([
            'orderID' => 'required|integer|exists:orders,id',
        ]);

        try {
            // Get the order ID from the request
            $orderID = $request->input('orderID');

            // Find the order and mark it as printed
            $order = $this->orderService->markAsPrinted($orderID);

            if (!$order) {
                return response()->json(['message' => 'Sipariş bulunamadı'], 404);
            }

            return response()->json($order, 200);

        } catch (\Throwable $e) {
            // Log the exception
            \Log::error('Error marking order as printed: '.$e->getMessage(), [
                'exception' => $e,
                'orderID' => $request->input('orderID')
            ]);

            // Return a more descriptive error message
            return response()->json(['message' => 'Bir hata oluştu. Lütfen tekrar deneyin.'], 500);
        }
    }

    public function markAsChecked(Request $request){
          // Request'ten ürün kodunu al
          $productCode = $request->input('productCode');

          // Servis metodunu çağır ve sonucu al
          $response = $this->orderService->markAsChecked($productCode);

          // Servis metodunun döndürdüğü yanıtı geri döndür
          return $response;
    }
}

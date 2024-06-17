<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Interfaces\IMalzemos;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Carbon\Carbon;
use Validator;

use App\Models\Malzemos;



class MalzemosController extends Controller
{
    //
    private IMalzemos $malzemosService;

    public function __construct(IMalzemos $_malzemosService){
        $this->malzemosService = $_malzemosService;
    }

    public function index(Request $request){

            $search = $request->query('search');
            $per_page = $request->query('per_page');
            $depo_id = $request->query('depo_id');


            return $this->malzemosService->getMalzemos($search,$per_page,$depo_id);
    }

    public function getMalzemosByProductCode(Request $request){

        $depo_id=$request->query('depo_id');
        $product_code=$request->query('product_code');


        $malzemos = $this->malzemosService->getMalzemosByProductCode($product_code, $depo_id);

        if (!$malzemos) {
            abort(404, 'Malzeme bulunamadı');
        }

        return response()->json($malzemos,200);


    }

    public function addProductStock(Request $request){

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'adet' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $product_id=$request->id;
        $quantity=$request->adet;

        try {
            $stockMovement = $this->malzemosService->addProductToStock($product_id, $quantity);
            // Return a 200 OK response with the updated product data
            return response()->json($stockMovement, 200);
        } catch (\Exception $e) {
            // If an exception occurs, return a 500 Internal Server Error response with the exception message
            return response()->json(['error' => 'An error occurred while adding the product to stock.', 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteStockMovement(Request $request, $id){
        // Stok hareketini silmeye çalış
        try {
            $success = $this->malzemosService->deleteStockMovement($id);

            // Eğer silme işlemi başarılı olduysa, 200 OK yanıtı döndür
            if ($success) {
                return response()->json(['message' => 'Stok hareketi başarıyla silindi.'], 200);
            }

            // Eğer bir sebepten dolayı başarılı olmadıysa, 400 Bad Request yanıtı döndür
            return response()->json(['message' => 'Stok hareketi silinemedi.'], 400);
        } catch (\Exception $e) {
            // Eğer bir istisna oluştuysa, 500 Internal Server Error yanıtı döndür
            return response()->json(['error' => 'Stok hareketi silinirken bir hata oluştu.', 'message' => $e->getMessage()], 500);
        }
    }

    public function removeProductStock(Request $request){
        // Validate the request inputs
        $validator = Validator::make($request->all(), [
            'productCode' => 'required',  // Ensure 'id' is required, an integer, and exists in the 'products' table
            'adet' => 'required|integer|min:1',  // Ensure 'adet' is required, an integer, and at least 1
            'depoID'=> 'required|integer|exists:depos,id'
        ]);

        // If validation fails, return a 400 Bad Request response with errors
        if ($validator->fails()) {
            return response()->json('Gönderilen değerlerde bir hata var', 400);
        }

        // Extract the validated inputs
        $productCode = $request->input('productCode');
        $quantity = $request->input('adet');
        $depo_id = $request->input('depoID');

        $malzemos = $this->malzemosService->getMalzemosByProductCode($productCode, $depo_id);

        if (!$malzemos) {
            abort(404, 'Malzeme bulunamadı');
        }

        // Try to remove the product from the stock
        try {
            $stockMovement = $this->malzemosService->removeProductFromStock($malzemos->id, $quantity);

            // Return a 200 OK response with the stock movement data
            return response()->json($stockMovement, 200);
        } catch (\Exception $e) {
            // If an exception occurs, return a 500 Internal Server Error response with the exception message

            return response()->json($e->getMessage(), 400);
        }
    }

    public function store(Request $request)
    {
        $type='';

        if ($request->product['type'] == 'OUT') $type='OUT';
        if ($request->product['type'] == 'IN') $type='IN';

        $malzemos =  $this->malzemosService->saveProduct($request->product);

        if (!$malzemos) {
            abort(404, 'Malzeme bulunamadı');
        }

        // Try to remove the product from the stock
        try {
            if ($type=='OUT')
                $stockMovement = $this->malzemosService->removeProductFromStock($malzemos->id, 1);

            if ($type=='IN')
                 $stockMovement = $this->malzemosService->addProductToStock($malzemos->id, $request->product['adet']);


            // Return a 200 OK response with the stock movement data
            return response()->json($stockMovement, 200);
        } catch (\Exception $e) {
            // If an exception occurs, return a 500 Internal Server Error response with the exception message

            return response()->json($e->getMessage(), 400);
        }
    }

    public function newProduct(Request $request){
        try {
        $malzemos =  $this->malzemosService->saveProduct($request->product);


        return response()->json($malzemos->load('raf.descendants'), 200);
        } catch (\Exception $e) {
            // If an exception occurs, return a 500 Internal Server Error response with the exception message
            return response()->json($e->getMessage(), 400);
        }
    }

    public function update($id,Request $request){

        return response()->json($this->malzemosService->updateProductById($id,$request->product),200);

    }

    public function destroy($id)
    {
        return response()->json($this->malzemosService->deleteProductById($id),200);
    }
}

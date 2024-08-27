<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Models\User;
use App\Models\Products;
use App\Http\Resources\UserResource;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\CategoryComissionController;
use App\Http\Controllers\N11ProductController;
use App\Http\Controllers\ApiJobsController;
use App\Http\Controllers\ImageController;

use App\Http\Controllers\N11OrderListController;
use App\Http\Controllers\HBOrderListController;

use App\Http\Controllers\DepoController;
use App\Http\Controllers\MalzemosController;
use App\Http\Controllers\StockMovementsController;
use App\Http\Controllers\HbListingController;
use App\Http\Controllers\HbCatalogController;


use App\Http\Controllers\HakedisController;


use App\Http\Controllers\PazaramaTestController;


use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use App\Jobs\SendProductsPriceToN11;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schedule;


Route::get('getBrands', [PazaramaTestController::class, 'getBrands']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    $user = $request->user();
    return response()->json(new UserResource(User::findOrFail($user->id)));
});


Route::group(['middleware' => ['auth:sanctum']], function () {


});

Route::apiResource('products',ProductController::class);
Route::apiResource('orders',OrderController::class);


Route::post('addCategory', [ProductCategoryController::class, 'addCategory']);
Route::resource('product_categories', ProductCategoryController::class)->shallow();



Route::post('addDepo', [DepoController::class, 'addCategory']);


Route::post('addProductCoverImage', [ProductController::class, 'addProductCoverImage']);
Route::post('matchN11Product', [ProductController::class, 'matchN11Product']);
Route::post('matchHbProduct', [ProductController::class, 'matchHbProduct']);
Route::get('getProductBySellerCode',[ProductController::class, 'getProductBySellerCode']);

Route::post('importHbCargoPricesFromFile', [CargoController::class, 'importHbCargoPricesFromFile']);

Route::get('getN11CargoPrices', [CargoController::class, 'getN11CargoPrices']);
Route::get('getCargoPriceFromN11', [CargoController::class, 'getCargoPriceFromN11']);

Route::get('getN11CategoryCommisionsFromN11', [CategoryComissionController::class, 'getN11CategoryCommisionsFromN11']);
Route::get('getN11CommissionRates', [CategoryComissionController::class, 'getN11CommissionRates']);
Route::get('getN11CategoryCommissionByCategoryId/{n11CategoryId}', [CategoryComissionController::class, 'getN11CategoryCommissionByCategoryId']);

Route::get('getN11ProductBySellerCode/{sellerCode}',[N11ProductController::class, 'getN11ProductBySellerCode']);


Route::get('batches/{batch_id}',[ApiJobsController::class, 'addUpdateN11ProductsPriceStock']);
Route::get('findBatchIdByName/{name}',[ApiJobsController::class, 'findBatchIdByName']);
Route::get('products-price-update-to-n11-quee',[ApiJobsController::class, 'updateN11Prices']);

Route::post('/confirm-item', [OrderController::class, 'confirmItem']);

Route::post('uploadProductImages', [ImageController::class, 'uploadImages']);
Route::delete('deleteProductImages/{image_id}', [ImageController::class, 'deleteImages']);
Route::post('changeImageOrder', [ImageController::class, 'changeImageOrder']);

Route::get('getOrderListFromN11', [N11OrderListController::class, 'getOrderListFromN11']);
Route::get('getOrderListFromHB', [HBOrderListController::class, 'getOrderListFromHB']);

Route::get('getListingsFromHB', [HbListingController::class, 'getListingFromHb']);
Route::get('getHBOrderDetailByOrderNumber/{ordernumber}',[HBOrderListController::class, 'getHBOrderDetailByOrderNumber']);
Route::get('getAllProductsFromHB', [HbCatalogController::class, 'getAllProductsFromHB']);
Route::get('getAllProductsFromHBByStatus',[HbCatalogController::class, 'getAllProductsFromHBByStatus']);


Route::get('getHbListingFromHbBySku/{hbSku}',[HbListingController::class, 'getHbListingFromHbBySku']);
Route::get('getHbListingByMerchantSku/{merchantSku?}',[HbListingController::class, 'getHbListingByMerchantSku']);

//HAKEDİŞ
Route::post('addHakedisItem', [HakedisController::class, 'addHakedisItem']);
Route::get('hakedis/daily', [HakedisController::class, 'listHakedisByDay']);

//DEPO PROGRAMI ROUTES
Route::apiResource('malzemos',MalzemosController::class);
Route::resource('get_depos', DepoController::class)->shallow();
Route::get('getMalzemosByProductCode',[MalzemosController::class, 'getMalzemosByProductCode']);
Route::post('addProductStock',[MalzemosController::class, 'addProductStock']);
Route::delete('deleteStockMovement/{id}',[MalzemosController::class, 'deleteStockMovement']);
Route::post('removeProductStock',[MalzemosController::class, 'removeProductStock']);
Route::apiResource('stockmovements',StockMovementsController::class);
Route::post('newProduct',[MalzemosController::class, 'newProduct']);

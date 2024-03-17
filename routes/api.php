<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Models\User;
use App\Http\Resources\UserResource;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\CategoryComissionController;
use App\Http\Controllers\N11ProductController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    $user = $request->user();
    return response()->json(new UserResource(User::findOrFail($user->id)));
});


Route::group(['middleware' => ['auth:sanctum']], function () {


});

Route::apiResource('products',ProductController::class);
Route::post('addCategory', [ProductCategoryController::class, 'addCategory']);

Route::resource('product_categories', ProductCategoryController::class)->shallow();
Route::post('addProductCoverImage', [ProductController::class, 'addProductCoverImage']);

Route::post('importHbCargoPricesFromFile', [CargoController::class, 'importHbCargoPricesFromFile']);

Route::get('getN11CargoPrices', [CargoController::class, 'getN11CargoPrices']);
Route::get('getCargoPriceFromN11', [CargoController::class, 'getCargoPriceFromN11']);

Route::get('getN11CategoryCommisionsFromN11', [CategoryComissionController::class, 'getN11CategoryCommisionsFromN11']);
Route::get('getN11CommissionRates', [CategoryComissionController::class, 'getN11CommissionRates']);

Route::get('getN11ProductBySellerCode/{sellerCode}',[N11ProductController::class, 'getN11ProductBySellerCode']);

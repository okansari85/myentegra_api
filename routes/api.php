<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Models\User;
use App\Http\Resources\UserResource;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\CategoryComissionController;

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

Route::resource('product_categories', ProductCategoryController::class)->shallow();
Route::post('addCategory', [ProductCategoryController::class, 'addCategory']);
Route::apiResource('products',ProductController::class);
Route::post('addProductCoverImage', [ProductController::class, 'addProductCoverImage']);
Route::get('getCargoPriceFromN11', [CargoController::class, 'getCargoPriceFromN11']);
Route::post('importHbCargoPricesFromFile', [CargoController::class, 'importHbCargoPricesFromFile']);
Route::get('getN11CategoryCommisions', [CategoryComissionController::class, 'getN11CategoryCommisions']);

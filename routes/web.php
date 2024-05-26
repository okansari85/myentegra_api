<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

use App\Http\Controllers\OrderListController;


Route::get('/', function () {
    return view('welcome');
});


Route::post('login',[AuthController::class, 'login']);
Route::post('logout',[AuthController::class, 'logout']);


Route::get('order_list',[OrderListController::class, 'getOrderListFromN11']);

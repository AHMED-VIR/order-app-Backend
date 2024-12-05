<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WishListController;
use AppHttpControllers\StoreController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/cart/items',[CartController::class,'showUserCart']);
Route::get('/cart/ids',[CartController::class,'showUserCartIds']);
Route::delete('/cart/delete',[CartController::class,'delete']);
Route::apiResource('/cart',CartController::class);

Route::delete('/wishlist/delete',[WishListController::class,'delete']);
Route::get('/wishlist/items',[WishListController::class,'showUserWishList']);
Route::get('/wishlist/ids',[WishListController::class,'showUserWishListIds']);
Route::apiResource('/wishlist',WishListController::class);

Route::get('/order/view',[OrderController::class,'showUserOrders']);
Route::apiResource('/order',OrderController::class);
Route::apiResource('/store',StoreController::class);

Route::apiResource('/products',ProductController::class);

Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);
Route::post('/logout',[AuthController::class,'logout']);

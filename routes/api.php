<?php
use App\Http\Controllers\AdminController;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\WishListController;
use App\Http\Controllers\StoreController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/cart/items',[CartController::class,'showUserCart']);
Route::apiResource('/cart',CartController::class);

Route::delete('/wishlist/delete',[WishListController::class,'delete']);
Route::get('/wishlist/items',[WishListController::class,'showUserWishList']);
Route::apiResource('/wishlist',WishListController::class);

Route::get('/order/view',[OrderController::class,'showUserOrders']);
Route::apiResource('/order',OrderController::class);


Route::apiResource('/store',StoreController::class);

Route::post('/admin/register',[AdminController::class,'register']);
Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);
Route::post('/logout',[AuthController::class,'logout']);

<?php
use App\Http\Controllers\AdminController;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
<<<<<<< HEAD
use App\Http\Controllers\SearchController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\WishListController;
=======
use App\Http\Controllers\WishListController;
use AppHttpControllers\StoreController;
>>>>>>> d8f63acd54b8fb7793a1d76cdc75120e53fdd65f
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
<<<<<<< HEAD

=======
Route::apiResource('/store',StoreController::class);
>>>>>>> d8f63acd54b8fb7793a1d76cdc75120e53fdd65f

Route::apiResource('/products',ProductController::class);

<<<<<<< HEAD
Route::apiResource('/store',StoreController::class);

Route::get('search/{query}',[SearchController::class,'index']);

=======
>>>>>>> d8f63acd54b8fb7793a1d76cdc75120e53fdd65f
Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);
Route::post('/logout',[AuthController::class,'logout']);
Route::post('/user/changeInfo',[AuthController::class,'changeInfo']);
Route::post('/user/wallet',[AuthController::class,'userWallet']);
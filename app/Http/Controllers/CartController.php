<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class CartController extends Controller implements HasMiddleware
{
   
    public static function middleware()
    {
        return[
            new Middleware('auth:sanctum')
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return['itmes'];
    }

    public function showUserCart(Request $request)
    {
        $cart_items = Cart::where('user_id',$request->user()->id)
        ->with('product')->get();
        return response()->json([
            "sucess"=>true,
            "message"=>"showing cart items",
            "cart_items"=>$cart_items
        ],200);
    }
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $fields = $request->validate(
            [
                'product_id'=>"required|exists:products,id|integer"
            ]
        );
        if(Cart::where('user_id',$request->user()->id)->where('product_id',$request->product_id)->exists() ){
            return response()->json([
                'success'=>false,
                'message'=> 'product already in cart',
            ],400);
        }
        $cart_item = Cart::create([
            'user_id' => $request->user()->id, 
            'product_id' => $fields['product_id'],
        ]);
        return response()->json(
            [
                "success"=>true,
                "message"=>"Item added to cart successfully",
                "cart_item"=>$cart_item
            ],
        201);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cart $cart)
    {
        Gate::authorize('modify',$cart);
        $cart->delete();
        return response()->json([
            'success'=>true,
            'message'=>"item cart deleted successfully"
        ]);
    }
}

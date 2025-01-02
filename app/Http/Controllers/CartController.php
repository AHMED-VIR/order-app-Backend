<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
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
            "success"=>true,
            "message"=>"showing cart items",
            "cart_items"=>$cart_items
        ],200);
    }
    
    public function showUserCartIds(Request $request){
        $ids =  Cart::where('user_id',$request->user()->id)->select('product_id')->get();
        return response()->json([
            'success'=>true,
            'message'=>'showing user cart products ids',
            'cart_ids'=>$ids
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    DB::beginTransaction();
    try {
        $fields = $request->validate([
            'product_id' => 'required|exists:products,id|integer',
            'product_count' => 'required|integer|min:1'
        ]);

        $product = Product::findOrFail($fields['product_id']);
        $cart_item = Cart::where('user_id', $request->user()->id)
            ->where('product_id', $fields['product_id']);

            
        $currentStock = $product->stock;

        if (($fields['product_count']) > $currentStock) {
            return response()->json([
                "success" => false,
                "message" => "invalid quantity",
            ], 400);
        }

        if ($cart_item->exists()) {
            $currentStock = $product->stock;
            $currentCartCount = $cart_item->value('product_count');

            if (($currentCartCount + $fields['product_count']) > $currentStock) {
                return response()->json([
                    "success" => false,
                    "message" => "item out of stock",
                ], 400);
            } else {
                $cart_item->increment('product_count',$fields['product_count']);
                $updatedCartItem = $cart_item->first();
                DB::commit();
                return response()->json([
                    "success" => true,
                    "message" => "Item added to cart successfully",
                    "cart_item" => $updatedCartItem,
                    'price'=>  ($currentCartCount + 1) * ($product->value('price')),
                ], 201);
            }
        }

        $newCartItem = Cart::create([
            'user_id' => $request->user()->id,
            'product_id' => $fields['product_id'],
            'product_count' => $fields['product_count'],
            'price'=> (1) * ($product->value('price'))
        ]);

        DB::commit();
        return response()->json([
            "success" => true,
            "message" => "Item added to cart successfully",
            "cart_item" => $newCartItem
        ], 201);
    } catch (\Exception $error) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => $error->getMessage(),
            'line' => $error->getLine(),
            'file' => $error->getFile(),
        ], 500);
    }
}



    public function delete(Request $request)
    {
        $fields = $request->validate([
            'item_id'=>'required|integer'
        ]);

        if(!Cart::where('product_id',$fields['item_id'])->where('user_id',$request->user()->id)->exists()){
            return response()->json([
                'success'=>false,
                'message'=>'item does not exist'
            ],404);
        }
        $cart = Cart::where('product_id',$fields['item_id'])->where('user_id',$request->user()->id)->get()->first();
        if($cart->product_count > 1){
            $cart->decrement('product_count',1);
            return response()->json([
                'success'=>true,
                'message'=>'item removed from cart successfully'
            ],200);    
        }
        $cart->delete();
        return response()->json([
            'success'=>true,
            'message'=>'item removed from cart successfully'
        ],200);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum')
        ];
    } 
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate(
            [
                'items'=>'required|array',
                'items.*.product_id'=>'required|exists:products,id',
                'items.*.quantity'=>'required|integer|min:1',
            ]
        );
        DB::beginTransaction(); 
        try{
            $order = Order::create([
                'user_id'=>$request->user()->id,
                'total_amount'=>0,
                'status'=>'pending'
            ]);

            $totalAmount = 0;

            foreach($validate['items'] as $item){
                $product = Product::findOrFail($item['product_id']);
                OrderItem::create([
                    'order_id'=>$order->id,
                    'product_id'=>$product->id,
                    'price'=>$product->price,
                    'quantity'=>$item['quantity']
                    ]
                );
                $totalAmount += $product['price'] * $item['quantity'];
            }

            $order->update(['total_amount'=>$totalAmount]);
            DB::commit();
            return response()->json(
                [
                    'success'=>true,
                    'message'=>'order created successfully',
                    'order'=>$order->load('orderItems.product'),
                ]
            ,201);
        }catch(\Exception $err){
            DB::rollBack();
            return response()->json([
                'success'=>false,
                'message'=>'faild to create order',
                'err'=>$err
            ],500);
        }
    }

    public function showUserOrders(Request $request){
        $orders = Order::where('user_id', $request->user()->id)->latest()->get();
        return response()->json([
        'success' => true,
        'message' => 'Viewing user orders',
        'orders' => $orders,
        ],200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        Gate::authorize('modify',$order);
        $result = $order->with('orderItems.product')->get();
        return response()->json([
            'success' => true,
            'message' => 'Viewing user orders',
            'orders' => $result,
            ],200);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
       
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        Gate::authorize('modify',$order);
        $order->delete();
        return response()->json(
            [
                'success'=>true,
                'messsage'=>'order is deleted'
            ]
        );
    }
}

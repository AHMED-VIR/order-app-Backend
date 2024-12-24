<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProductController extends Controller implements HasMiddleware
{

    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum',except:['index','show','getStoreProducts'])
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::where('stock','>',0)
        ->select('id','name','price','rating','stock')->get();
        return response()->json(
            [
                'success'=>true,
                'message'=>'showing all products',
                'product'=>$products
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'name'=>'required|max:255|string',
            'stock'=>'required|integer',
            'price'=>'required|numeric',
            'store_id'=>'required|exists:stores,id|integer',
            'image'=> 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'description'=>'nullable|string'
        ]);
        $product = Product::create($fields);
        return response()->json([
            'success'=>true,
            'message'=>'product added successfully',
            'product'=>$product
        ]
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $result = $product->with('store');
        return response()->json(
            [
                'success'=>true,
                'message'=>'showing product details',
                'product'=>$product
            ]
        );
    }

    public function getStoreProducts($storeId){
        $store = Store::where('id',$storeId);
        if(!$store->exists()){
            return response()->json(
                [
                    'success'=>false,
                    'message'=>'store not found'
                ],
                404
            );
        }
        $products = Product::where('store_id',$storeId)->get();
        return response()->json(
            [
                'success'=>true,
                'message'=>'showing store products',
                'products'=>$products
            ],
            200
        ); 
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
     
        $fields = $request->validate([
            'name'=>'required|max:255|string',
            'stock'=>'required|integer',
            'price'=>'required|numeric',
            'description'=>'nullable|string'
        ]);


        if(isset($feilds['name'])){
            $product->name = $feilds['name'];
        }

        if(isset($feilds['stock'])){
            $product->stock = $feilds['stock'];
        }

        if(isset($feilds['price'])){
            $product->price = $feilds['price'];
        }

        if(isset($feilds['description'])){
            $product->description = $feilds['description'];
        }

        $product->save();

        return response()->json([
            'success' => true,
            'message' => "Product information updated successfully",
            'data' => $product,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}

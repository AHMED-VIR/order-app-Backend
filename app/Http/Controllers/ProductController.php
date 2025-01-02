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
        $products = Product::where('stock','>',0)->with('store:id,name')
        ->select('id','name','price','rating','stock','description','image','store_id')->get();
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
            'image'=> 'nullable|string',
            'description'=>'nullable|string'
        ]);
        $product = Product::create($fields);
        return response()->json([
            'success'=>true,
            'message'=>'product added successfully',
            'product'=>$product
        ]
        ,201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $result = Product::where('id',$product->id)->with('store:id,name')
        ->select('id','name','price','rating','stock','description','image','store_id')->get()->first();

        return response()->json(
            [
                'success'=>true,
                'message'=>'showing product details',
                'product'=>$result,
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
        $products = Product::where('store_id',$storeId)->with('store:id,name')
        ->select('id','name','price','rating','stock','description','image','store_id')->get();
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
            'name'=>'nullable|max:255|string',
            'stock'=>'nullable|integer',
            'price'=>'nullable|numeric',
            'description'=>'nullable|string'
        ]);


        if(isset($fields['name'])){
            $product->name = $fields['name'];
        }

        if(isset($fields['stock'])){
            $product->stock = $fields['stock'];
        }

        if(isset($fields['price'])){
            $product->price = $fields['price'];
        }

        if(isset($fields['description'])){
            $product->description = $fields['description'];
        }

        if(isset($fields['image'])){
            $product->image = $fields['image'];
        }

        $product->save();

        return response()->json([
            'success' => true,
            'message' => "Product information updated successfully",
            'data' => $product,
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProductController extends Controller implements HasMiddleware
{

    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum',except:['index','show'])
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::where('stock','>',0)
        ->select('id','name','price','rating')->get();
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

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}

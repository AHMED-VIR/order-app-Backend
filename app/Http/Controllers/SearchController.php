<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\Request;

class SearchController extends Controller 
{
    /**
     * Display a listing of the resource.
     */
    public function index($query)
    {

        if (empty($query)) {
            return response()->json([
                'success' => false,
                'message' => 'Query parameter is required.',
            ], 400);
        }

        if($query == 'rated'){
            $products = Product::orderBy('rating','desc')->limit(10)->get();
            return response()->json(
                [
                    'success'=>true,
                    'data'=>[
                        'products'=>$products,
                    ]
                ]
            );
        }

        $products = Product::where('name','LIKE',"$query%")->get();
        $stores = Store::where('name','LIKE',"$query%")->get();
        return response()->json(
            [
                'success'=>true,
                'data'=>[
                    'products'=>$products,
                    'stores'=>$stores
                ]
            ]
        );
    }
}

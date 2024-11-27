<?php

namespace App\Http\Controllers;

use App\Models\WishList;
use App\Http\Requests\StoreWishListRequest;
use App\Http\Requests\UpdateWishListRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class WishListController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum',except:['index'])
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return 'wishlist  index';
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'product_id'=>'required|exists:products,id|integer'
        ]); 
        if(WishList::where('user_id',$request->user()->id)->where('product_id',$fields['product_id'])->exists()){
            return  response()->json([
              'success'=>false,
              'message'=>'item already exists'
            ]);
        }
        $wishlist = WishList::create(['user_id'=>$request->user()->id,'product_id'=>$fields['product_id']]);
        return  response()->json([
            'success'=>true,
            'message'=>'item added to wishlist',
            'wishlistItem'=>$wishlist
          ]);
    }


    public function showUserWishlist(Request $request){
        $wishList = WishList::where('user_id',$request->user()->id)->with('product')->get();
        return response()->json([
            'success'=>true,
            'message'=>'showing user wishlist',
            'wishlist'=>$wishList
        ]);
    }


    public function delete(Request $request){
       
        $fields = $request->validate([
            'item_id'=>'required|integer'
        ]);

        if(!WishList::where('id',$fields['item_id'])->exists()){
            return response()->json([
                'success'=>false,
                'message'=>'item does not exist'
            ],404);
        }
        $wishList = WishList::where('id',$fields['item_id'])->get()->first();
        Gate::authorize('modify',$wishList);
        $wishList->delete();
        return response()->json([
            'success'=>true,
            'message'=>'item removed from wishlist successfully'
        ]);
    }
}

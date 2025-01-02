<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Store;
use AppModelsStore;
use AppHttpRequestsStoreStoreRequest;
use AppHttpRequestsUpdateStoreRequest;
use Illuminate\Http\Request;
use IlluminateHttpRequest;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stores = Store::get();
        return response()->json([
            'success'=>true,
            'data'=>$stores
        ],200);
    }
    public function store(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|max:255',
            'type'=> 'required',
            'number'=>'required|numeric|min:10',
            'location'=>'required',
            'image'=> 'nullable|string',
            'description'=>'required|string'
        ]);
        $store= Store::create($fields);
        return response()->json([
            'success'=>true,
            'message'=>'Store created successfully',
            'data'=>$store
        ],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Store $store)
    {
        return response()->json([
            'success'=>true,
            'data'=>$store
        ],200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Store $store)
    {
        $fields = $request->validate([
            'name' => 'nullable|max:255',
            'type'=> 'nullable',
            'number'=>'nullable|numeric|min:10',
            'location'=>'nullable',
            'image'=> 'nullable|string',
            'description'=>'nullable|string'
        ]);


        if(isset($fields['name'])){
            $store->name = $fields['name'];
        }

        if(isset($fields['location'])){
            $store->location = $fields['location'];
        }

        if(isset($fields['type'])){
            $store->type = $fields['type'];
        }

        if(isset($fields['number'])){
            $store->number = $fields['number'];
        }

        if(isset($fields['description'])){
            $store->description = $fields['description'];
        }

        if(isset($fields['image'])){
            $store->image = $fields['image'];
        }

        $store->save();

        return response()->json([
            'success' => true,
            'message' => "Store information updated successfully",
            'data' => $store,
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Store $store)
    {
        $store->delete();
        
        return response()->json([
            'success'=>true,
            'message'=>'Store deleted successfully'
        ],200);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller implements HasMiddleware
{

    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum',except:['login','register'])
        ];
    }

     public function register(Request $request){
       
        $fields = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:3|confirmed',
            'image'=> 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'phone_number'=>'numeric|min:8',
            'Location'=>'required'
        
        ]);

        $profileImagePath = null;
        if($request->hasFile('image')){
            $profileImagePath = $request->file('image')->store('profile_images','public');
        }

        $user = User::create([
            'email'=>$fields['email'],
            'password'=>bcrypt($fields['password']),
            'name'=>$fields['name'],
            'profile_image'=>$profileImagePath,
            'is_admin'=>true,
            'phone_number'=>$fields['phone_number'],
            'Location'=>$fields['Location']
        ]);

        
        $token = $user->createToken($request->email)->plainTextToken;
        return response()->json([
            'success'=>true,
            'message'=>"User registered successfully",
            'data'=>[
                'user'=>$user,
                'token'=>$token
            ],
        ],201);
    }
}



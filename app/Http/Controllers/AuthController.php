<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller implements HasMiddleware
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
            'image'=> 'nullable|image|mimes:png,jpg,jpeg|max:2048' 
        ]);

        $profileImagePath = null;
        if($request->hasFile('image')){
            $profileImagePath = $request->file('image')->store('profile_images','public');
        }

        $user = User::create([
            'email'=>$fields['email'],
            'password'=>bcrypt($fields['password']),
            'name'=>$fields['name'],
            'profile_image'=>$profileImagePath
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

    public function login(Request $request){
        $fields = $request->validate([
            'email'=>"email|exists:users|required",
            'password'=>"required",
        ]);
        $user = User::where('email',$request->email)->first();
        if(!$user || !Hash::check($request->password,$user->password)){
            return response()->json([
                'success'=>false,
                'message'=>'Invalid email or password'
            ],403);
        }
        $token = $user->createToken($request->email)->plainTextToken;
        return response()->json([
            'success'=>true,
            'message'=>"Login successfully",
            'data'=>[
                'user'=>$user,
                'token'=>$token
            ],
        ],200);
    }

    public function logout(Request $request){
        $request->user()->tokens()->delete();
        return response()->json([
            'success'=>true,
            'message'=>"Logout successfully"
        ],200);
    }
}

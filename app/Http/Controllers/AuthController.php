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
            'password' => 'required|min:3|confirmed',
            'image'=> 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'phone_number'=>'required|numeric|min:3|unique:users',
            'Location'=>'required',
            'is_admin'=>'required'
        ]);

        $profileImagePath = null;
        if($request->hasFile('image')){
            $profileImagePath = $request->file('image')->store('profile_images','public');
        }

        $user = User::create([
            'password'=>bcrypt($fields['password']),
            'name'=>$fields['name'],
            'profile_image'=>$profileImagePath,
            'is_admin'=>$fields['is_admin'],
            'phone_number'=>$fields['phone_number'],
            'Location'=>$fields['Location']
        ]);

        
        $token = $user->createToken($request->name)->plainTextToken;
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
            'phone_number'=>"numeric|exists:users|required",
            'password'=>"required",
        ]);
        $user = User::where('phone_number',$request->phone_number)->first();
        if(!$user || !Hash::check($request->password,$user->password)){
            return response()->json([
                'success'=>false,
                'message'=>'Invalid phone number or password'
            ],403);
        }
        $token = $user->createToken($request->phone_number)->plainTextToken;
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

    public function changeInfo(Request $request){
        $feilds = $request->validate([
            'name'=>'sometimes|string|max:255',
            'phone_number'=>'sometimes|numeric|unique:users,phone_number',
            'Location' => 'sometimes|string',
        ]);

        $user = $request->user();

        if(isset($feilds['name'])){
            $user->name = $feilds['name'];
        }

        if(isset($feilds['phone_number'])){
            $user->phone_number = $feilds['phone_number'];
        }

        if(isset($feilds['Location'])){
            $user->Location = $feilds['Location'];
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => "User information updated successfully",
            'data' => $user,
        ], 200);

    }

    public function userWallet(Request $request){
        $fields = $request->validate([
            'ammount'=>'required|numeric',
            'password'=>'required'
        ]);
        $user = $request->user();
        if(!Hash::check($request->password,$user->password)){
            return response()->json([
                'success' => false,
                'errors' => ['password' => ['incorrect password']],
            ], 401);
        }
        $user->increment('wallet', $fields['ammount']);
        return response()->json([
            'success' => true,
            'message' => "ammount added to user wallet successfully",
            'data' => $user,
        ], 200);
    }
}


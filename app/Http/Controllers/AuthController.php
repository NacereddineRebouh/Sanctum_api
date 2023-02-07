<?php

namespace App\Http\Controllers;

use App\Models\User;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;
use App\Http\Controllers\Cookie;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $fields=$request->validate([
            'name'=>'required|string',
            'email'=>'required|string|unique:users,email',
            'password'=>'required|string|confirmed',
        ]);


        $user=User::Create([
            'name'=>$fields['name'],
            'email'=>$fields['email'],
            'password'=>bcrypt($fields['password']),
        ]);

        $token = $user->createToken('mayToken')->plainTextToken;
        $response=[
            'user'=>$user,
            'token'=>$token
        ];

        return response($response, 201);//201:: Done & Something was created
    }
	
	// public function registerGoogle(Request $request)
    // {
        // $fields=$request->validate([   
            // 'email'=>'required|string',
        // ]);


        // $user=User::Create([
            // 'name'=>$fields['name'],
            // 'email'=>$fields['email'],
            // 'password'=>bcrypt($fields['email']),
        // ]);

        // $token = $user->createToken('mayToken')->plainTextToken;
        // $response=[
            // 'user'=>$user,
            // 'token'=>$token
        // ];

        // return response($response, 201);//201:: Done & Something was created
    // }

    public function logout(request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response(['message'=>'Logged out successfully']);
    }

    public function gettoken(request $request)
    {
        $token=$request->user();
        $token2=$request->bearerToken();

        return response()->json(['token1'=>$token]);
    }

    public function login(request $request)
    {
        $fields=$request->validate([
            'email'=>'required|string',
            'password'=>'required|string'
        ]);

        $userEmail=User::where('email', $fields['email'])->first();
        $userName=User::where('name', $fields['email'])->first();
        $user = $userEmail;
        if (!$userEmail) {
            $user = $userName;
        }
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response(['message'=>'Bad creds'], 401);//unauthorized
        }
        $token = $user->createToken('myToken')->plainTextToken;
        $response=[
            'user'=>$user,
            'token'=>$token
        ];
        $cookie=Cookie::make(
            'Access-Token',
            $token,
            14400, // time to expire
            null,
            null,
            false,
            true,
            false,
            'none'//same-site   <-----
        );

        return response($response, 201)->withCookie($cookie);//201:: Done & Something was created
    }
}

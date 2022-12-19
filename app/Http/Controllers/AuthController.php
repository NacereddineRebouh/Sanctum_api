<?php

namespace App\Http\Controllers;

use App\Models\User;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;
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

    public function logout(request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response(['message'=>'Logged out successfully']);
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

        return response($response, 201);//201:: Done & Something was created
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //
    public function authenticate(Request $request)
    {
        $this->validate($request,[
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $credentials = $request->only(['email','password']);

        if(!$token = auth()->attempt($credentials)){
            return response()->json(['error'=>'Incorrect credentials'],401);
        }
        return response()->json(compact('token'));
    }

    public function register(Request $request)
    {
        $inputs = $this->validate($request,[
            'email' => 'required|email|max:255|unique:users',
            'name' => 'required|max:255',
            'password' => 'required|min:8|confirmed',
        ]);
        $inputs['password'] = Hash::make($inputs['password']);
        $user = User::create($inputs);
        $token = JWTAuth::fromUser($user);
        return response()->json(['token'=>$token]);
    }
}

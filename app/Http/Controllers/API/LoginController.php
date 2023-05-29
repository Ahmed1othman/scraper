<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request){
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
            'device_token' => 'required|string',
        ]);


        $credentials = [
            'phone' => $request->phone,
            'password' => $request->password,
        ];


        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = $request->user();
        $token = $user->createToken($request->device_token)->plainTextToken;

        $user->fcm_token = $request->device_token;
        $user->save();
        return response()->json([
            'access_token' => $token,
            'user_status' => $user->status,
        ]);
    }



    public function logout(Request $request)
    {
        Auth::guard('api')->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

}

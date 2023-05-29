<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\ChangePasswordRequest;
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
        $user->tokens()->delete();
        $token = $user->createToken($request->device_token)->plainTextToken;
        $user->fcm_token = $request->device_token;
        $user->save();
        return response()->json([
            'success'=>true,
            'message'=>'تم تسجيل الدخول بنجاح',
            'access_token' => $token,
            'user_status' => $user->status,
        ]);
    }



    public function logout()
    {
        $user = auth()->user();
        $user->tokens()->delete();
        $user->fcm_token = null;
        $user->save();
        $user->currentAccessToken()->delete();
        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الخروج بنجاح',
        ]);
    }

    public function changePassword(ChangePasswordRequest $request){
        $user = auth()->user();
        $user->update([
            'password'=>$request->new_password
        ]);
        $user->tokens()->delete();
        $user->currentAccessToken()->delete();
        $user->save();
    }


}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255'
            ],

            'mobile' => [
                'required',
                'string',
                'unique:users,mobile'
            ],

            'password' => [
                'required',
                'min:6'
            ],
        ]);


        $user = User::create([
            'name' => $data['name'],
            'mobile' => $data['mobile'],
            'password' => $data['password'],
            'role' => 'resident',
        ]);


        $token = $user->createToken('mobile-app')->plainTextToken;


        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'mobile' => [
                'required',
                'string'
            ],

            'password' => [
                'required'
            ],
        ]);


        $user = User::where('mobile', $data['mobile'])
            ->first();


        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }


        $token = $user->createToken('mobile-app')->plainTextToken;


        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user()
        ]);
    }


    public function logout(Request $request)
    {
        $request->user()
            ->currentAccessToken()
            ->delete();


        return response()->json([
            'message'=>'خروج با موفقیت انجام شد'
        ]);
    }


}

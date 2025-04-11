<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // handle registration
    public function register(Request $request)
    {
        // vlaidate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
        // create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        // create the token
        $token = $user->createToken('api')->plainTextToken;
        // return the user
        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    // handle login
    public function login(Request $request)
    {
        // validate the request
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // find the user
        $user = User::where('email', $credentials['email'])->first();
        // check if the user exists
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        // create the token
        $token = $user->createToken('api')->plainTextToken;

        // return the user
        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    // handle logout
    public function logout(Request $request)
    {
        // revoke the token
        $request->user()->currentAccessToken()->delete();
        
        // return response
        return response()->json([
            'message' => 'Logged out successfully',
        ], 200);
    }   

    // handle user profile
    public function profile(Request $request)
    {
        // return the user
        return response()->json([
            'user' => $request->user(),
        ], 200);
    }
}

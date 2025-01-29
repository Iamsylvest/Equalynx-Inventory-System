<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Http\Resources\UserResource; // Import the UserResource

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            $user = Auth::user();
            // Generate token
            $token = $request->user()->createToken('YourAppName_Token_' . Str::random(40))->plainTextToken;


            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
            ]);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }
    public function logout(Request $request)
    {
        // Revoke the current token
        $request->user()->currentAccessToken()->delete();
    
        // Optionally, you can revoke all tokens if you prefer
        // $request->user()->tokens->each(function ($token) {
        //     $token->delete();
        // });
    
        return response()->json(['message' => 'Logged out successfully']);
    }

    public function getUser(Request $request)
    {
        // Return the user resource, which will hide the email field
        return new UserResource($request->user());
    }

    // Other methods like login, logout, etc.

}
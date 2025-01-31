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
        // Validate login credentials (using email and password)
        $credentials = $request->only('email', 'password');
        
        if (Auth::attempt($credentials)) {
            // Authentication passed, generate token
            $user = Auth::user();
            $token = $request->user()->createToken('YourAppName_Token_' . Str::random(40))->plainTextToken;
    
            // Return user and token
            return response()->json([
                'token' => $token,
                'user' => new UserResource($user), // Send the user data in the response
            ]);
        }
        
        return response()->json(['message' => 'Unauthorized'], 401);
    }
 
    public function logout(Request $request)
{
    $request->user()->tokens()->delete(); // Revoke all tokens
    return response()->json(['message' => 'Logged out'], 200);
}

    public function getUser(Request $request)
    {
        // Return the user resource, which will hide the email field
        return new UserResource($request->user());
    }

    // Other methods like login, logout, etc.

}
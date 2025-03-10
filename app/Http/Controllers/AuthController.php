<?php

namespace App\Http\Controllers;

use App\Events\UserLoggedIn;
use App\Events\UserLoggedOut;
use App\Events\ActivityLogged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Http\Resources\UserResource; // Import the UserResource
use Illuminate\Support\Facades\Log; // Import Log facade

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validate login credentials (using email and password)
        $credentials = $request->only('email', 'password');
        
        if (Auth::attempt($credentials)) {
            // Authentication passed, generate token
            $user = Auth::user();
               
        // Update status to 'active' in the database
        /** @var \App\Models\User $user **/
            $user->status = 'active';
            $user->save();
            
            $token = $request->user()->createToken('YourAppName_Token_' . Str::random(40))->plainTextToken;

                    // After the user is authenticated...
            event(new UserLoggedIn(Auth::user()));  // This triggers the broadcast event

            

            event(new ActivityLogged([
                'action' => auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name . ' login ',
                       

                'performed_by' => auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
                'role' => auth()->user()->role, // ✅ Include role of the performing user
                'timestamp' => now()->toDateTimeString(),
            ]));
                    
            // ✅ Write log to a file
            Log::channel('activity')->info(json_encode([
                'action' => auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name . ' login ',
                       

                'performed_by' => auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
                'role' => auth()->user()->role, // ✅ Ensure role is logged
                'timestamp' => now()->toDateTimeString(),
            ]));
            
            return response()->json([
                'token' => $token,
                'user' => new UserResource($user), // Send the user data in the response
            ]);
        }
        
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    public function logout(Request $request)
    {
        // Ensure the logged-in user is fetched correctly
        $user = Auth::user();
        
        if ($user) {
            // Update user status to 'inactive'
            /** @var \App\Models\User $user */
            $user->status = 'inactive';
            $user->save();
    
            // Debugging: Log instead of `dd()`
            Log::info("User {$user->id} status changed to: {$user->status}");
    
            // Trigger the UserLoggedOut event
            event(new UserLoggedOut($user));
    
            // Revoke all tokens (Sanctum Auth)
            $request->user()->tokens()->delete();
    

            event(new ActivityLogged([
                'action' => auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name . ' logout ',
                       

                'performed_by' => auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
                'role' => auth()->user()->role, // ✅ Include role of the performing user
                'timestamp' => now()->toDateTimeString(),
            ]));
                    
            // ✅ Write log to a file
            Log::channel('activity')->info(json_encode([
                'action' => auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name . ' logout ',
                       

                'performed_by' => auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
                'role' => auth()->user()->role, // ✅ Ensure role is logged
                'timestamp' => now()->toDateTimeString(),
            ]));
            // Invalidate session (For session-based auth)
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

    
            return response()->json(['message' => 'Logged out'], 200);
        }
    
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    public function getUser(Request $request)
    { 
        // Return the user resource, which will hide the email field
        return new UserResource($request->user());
    }

    // Other methods like login, logout, etc.

}
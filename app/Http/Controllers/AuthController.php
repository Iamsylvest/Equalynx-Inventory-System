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
use App\Events\AdminNotification;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
   
    public function login(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');
            $key = 'login' . $request->ip(); // Unique key per IP
    
            // Check if user has exceeded login attempts
            if (RateLimiter::tooManyAttempts($key, 5)) {
                return response()->json([
                    'message' => 'Too many login attempts. Please try again in ' . RateLimiter::availableIn($key) . ' seconds.'
                ], 429);
            }
    
            if (Auth::attempt($credentials)) {
                // ✅ Authentication successful
                $user = Auth::user();
                $user->status = 'active';
                /** @var \App\Models\User $user **/
                $user->save();
                
                $token = $user->createToken('YourAppName_Token_' . Str::random(40))->plainTextToken;
    
                event(new ActivityLogged([
                    'action' => $user->first_name . ' ' . ($user->middle_name ?? '') . ' ' . $user->last_name . ' login',
                    'performed_by' => $user->first_name . ' ' . ($user->middle_name ?? '') . ' ' . $user->last_name,
                    'role' => $user->role,
                    'timestamp' => now()->toDateTimeString(),
                ]));
    
                Log::channel('activity')->info(json_encode([
                    'action' => $user->first_name . ' ' . ($user->middle_name ?? '') . ' ' . $user->last_name . ' login',
                    'performed_by' => $user->first_name . ' ' . ($user->middle_name ?? '') . ' ' . $user->last_name,
                    'role' => $user->role,
                    'timestamp' => now()->toDateTimeString(),
                ]));
    
                RateLimiter::clear($key); // ✅ Reset failed attempts on successful login
    
                return response()->json([
                    'token' => $token,
                    'user' => new UserResource($user),
                ]);
            } 
    
            // ❌ Failed login: Increase attempt count AFTER checking credentials
            RateLimiter::hit($key, 60); // Lock for 60 seconds after 5 attempts
    
            Log::warning('Unauthorized login attempt with email: ' . $request->email);
    
            event(new AdminNotification([
                'type' => 'access_invalid',
                'action' => 'Unauthorized login attempt with email ' . $request->email,
                'timestamp' => now()->toDateTimeLocalString(),
            ]));
    
            return response()->json(['message' => 'Invalid email or password. Please try again.'], 401);
    
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage()); // Log any server error
    
            return response()->json([
                'message' => 'An unexpected error occurred. Please try again later.'
            ], 500);
        }
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
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
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use App\Mail\SendOtpMail;
use App\Models\User;
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
                // ✅ Generate OTP
                $otp = random_int(100000, 999999);
                $user->email_otp = $otp;
                $user->email_otp_expires_at = now()->addMinutes(5); // Expires in 5 minutes
    
                $user->save();
                
                // ✅ Send OTP email
                Mail::to($user->email)->send(new SendOtpMail($otp));
    
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
    
                // ✅ Return the response as JSON, no view rendering needed
                return response()->json([
                    'user' => new UserResource($user),
                    'message' => 'Login successful. Please check your email for the OTP.'
                ]);
            }
    
            // If authentication fails, return an error
            return response()->json([
                'error' => 'Unauthorized. Invalid credentials.'
            ], 401);
        } catch (\Exception $e) {
            // Log the exception and return a generic error response
            Log::error('Login error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An unexpected error occurred. Please try again later.'
            ], 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);
    
        $user = \App\Models\User::where('email', $request->email)->first();
    
        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }
    
        // Debugging logs
        Log::debug('User OTP:', ['email' => $user->email, 'otp' => $user->email_otp, 'requested_otp' => $request->otp]);
    
        if (
            $user->email_otp !== $request->otp ||
            !$user->email_otp_expires_at ||
            now()->greaterThan(Carbon::parse($user->email_otp_expires_at))
        ) {
            return response()->json(['message' => 'Invalid or expired OTP.'], 401);
        }
    
        // OTP is valid
        $user->email_otp = null;
        $user->email_otp_expires_at = null;
        $user->status = 'active';
        $user->save();
    
        Auth::login($user); // Optional if needed for session
    
        $token = $user->createToken('YourAppName_Token_' . Str::random(40))->plainTextToken;
    
        // Optional: Log successful login
        Log::channel('activity')->info(json_encode([
            'action' => $user->first_name . ' ' . ($user->middle_name ?? '') . ' ' . $user->last_name . ' verified OTP and logged in',
            'performed_by' => $user->first_name . ' ' . ($user->middle_name ?? '') . ' ' . $user->last_name,
            'role' => $user->role,
            'timestamp' => now()->toDateTimeString(),
        ]));
    
        return response()->json([
            'message' => 'OTP verified. Login successful.',
            'token' => $token,
            'user' => new UserResource($user),
        ]);
    }

    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
    
        $user = User::where('email', $request->email)->first();
    
        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }
    
        // Check if OTP was sent within the last 30 seconds (or any time limit you prefer)
        $timeDifference = now()->diffInSeconds($user->last_otp_sent_at);
    
        if ($user->last_otp_sent_at && $timeDifference < 30) {
            return response()->json(['message' => 'Please wait before requesting another OTP.'], 400);
        }
    
        $otp = rand(100000, 999999);
        $user->email_otp = $otp;
        $user->email_otp_expires_at = now()->addMinutes(5);
        $user->last_otp_sent_at = now(); // Update the last sent time
        $user->save();
    
        // Send OTP email
        try {
            Mail::to($user->email)->send(new SendOtpMail($otp));
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to send OTP email.'], 500);
        }
    
        return response()->json(['message' => 'OTP resent successfully.']);
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
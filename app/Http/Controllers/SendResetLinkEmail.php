<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;

class SendResetLinkEmail extends Controller
{
     // Show the form for requesting a password reset link
     public function showLinkRequestForm()
     {
         return view('auth.forgot-password');  // Return a view with the form
     }
 
     // Send the password reset link to the provided email
     public function sendResetLinkEmail(Request $request)
     {
         $request->validate(['email' => 'required|email']);
 
         $status = Password::sendResetLink(
             $request->only('email')
         );
 
         return $status == Password::RESET_LINK_SENT
                     ? response()->json(['message' => 'Reset link sent to your email!'], 200)
                     : response()->json(['error' => 'Failed to send reset link.'], 400);
     }
 
     // Show the form to reset the password
     public function showResetForm($token)
     {
         return view('auth.reset-password', ['token' => $token]);  // Return a view with reset form
     }
 
     // Handle the password reset
     public function reset(Request $request)
     {
         $request->validate([
             'email' => 'required|email',
             'password' => 'required|confirmed|min:8',
             'token' => 'required'
         ]);
 
         $status = Password::reset(
             $request->only('email', 'password', 'password_confirmation', 'token'),
             function ($user) use ($request) {
                 $user->forceFill([
                     'password' => bcrypt($request->password),
                 ])->save();
             }
         );
 
         return $status == Password::PASSWORD_RESET
                     ? response()->json(['message' => 'Password has been reset successfully!'], 200)
                     : response()->json(['error' => 'Failed to reset password.'], 400);
     }
}



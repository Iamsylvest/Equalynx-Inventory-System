<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;

class PasswordResetController extends Controller
{
    public function resetPassword(Request $request)
    {
        // Validate the username and password
        $validated = $request->validate([
            'username' => 'required|string', // Add any other validation as needed
            'password' => 'required|string|min:8', // Ensure the password meets your requirements
        ]);

        // Handle the password reset logic here (e.g., finding the user by username and updating the password)
        
        // Example logic to find the user by username (adjust based on your model)
        $user = User::where('username', $request->username)->first();
        
        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // Update the password (hash it before saving)
        $user->password = bcrypt($request->password);
        $user->save();

        return response()->json(['message' => 'Password reset successful!']);
    }
}
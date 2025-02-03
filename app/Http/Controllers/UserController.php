<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function store(Request $request)
    {
        // Validate request data
        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'role' => 'required|string',
            'is_active' => 'required|boolean',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|',
        ]);

        // Create the new user
        $user = User::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'role' => $request->role,
            'is_active' => $request->is_active,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Return the created user with a success message
        return response()->json([
            'message' => 'User added successfully',
            'user' => $user
        ], 201);
        }

        public function index()
        {
            $users = User::all(); // Retrieve all users from the database
            return response()->json(['users' => $users]); // Return the users as a JSON response
        }

            // delete user UserController.php
        public function destroy($id)
        {
            $user = User::find($id);
            if ($user) {
                $user->delete();
                return response()->json(['message' => 'User deleted successfully.']);
            } else {
                return response()->json(['message' => 'User not found.'], 404);
            }
        }

        public function update(Request $request, $id)
        {
            // Find the user by ID or fail if not found
            $user = User::findOrFail($id);
    
            // Validate the incoming request data
            $validated = $request->validate([
                'role' => 'required|string',
                'is_active' => 'required|boolean',
                // Add other fields you want to update, such as name, email, etc.
            ]);
    
            // Update the user with the validated data
            $user->update($validated);
    
            // Return a response with a success message and the updated user data
            return response()->json(['message' => 'User updated successfully', 'user' => $user]);
        }
    
}
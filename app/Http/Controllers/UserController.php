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

        public function index(Request $request)
        {
            $query = User::query();
        
            // Apply status filter if provided
            if ($request->has('status') && $request->status !== "") {
                $query->where('is_active', $request->status);
            }
        
            // Apply role filter if provided
            if ($request->has('role') && $request->role !== "") {
                $query->where('role', $request->role);
            }
        
            $totalUsers = $query->count(); // Get total filtered user count
            // Paginate the filtered results
            $users = $query->paginate(7); // Set pagination limit to 7
           
        
            return response()->json([
                'data' => $users->items(),       // Actual users for current page
                'total' => $totalUsers,           // Total users matching the filters
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
            ]);
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
            // Find the user by ID or return a 404 response if not found
            $user = User::findOrFail($id);

            // Validate incoming request data
            $validated = $request->validate([
                'first_name' => 'sometimes|string|max:255',
                'last_name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:users,email,' . $id, // Ensure email is unique except for this user
                'role' => 'required|string',
                'is_active' => 'required|boolean',
            ]);

            // Update the user with only the validated data
            $user->update($validated);

            // Return response with updated user data
            return response()->json([
                'message' => 'User updated successfully',
                'user' => $user
            ], 200);
        }
    
}
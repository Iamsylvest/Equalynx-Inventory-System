<?php

namespace App\Http\Controllers;

use App\Events\ActivityLogged;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log; // Import Log facade
use Illuminate\Support\Facades\File; // ✅ Import File facade
use Illuminate\Pagination\LengthAwarePaginator;
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
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|',
        ]);

        // Create the new user
        $user = User::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'role' => $request->role,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);


        event(new ActivityLogged([
            'action' => 'New user ' . $user->first_name . ' ' . $user->last_name . ' was created by ' . ' ' .
                        auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name . 
                        ' on ' . now()->toDayDateTimeString(),

            'performed_by' => auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
            'role' => auth()->user()->role, // ✅ Include role of the performing user
            'timestamp' => now()->toDateTimeString(),
        ]));
                
        // ✅ Write log to a file
        Log::channel('activity')->info(json_encode([
            'action' => 'New user ' . $user->first_name . ' ' . $user->last_name . ' was created by' . ' ' .
                        auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
            'performed_by' => auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
            'role' => auth()->user()->role, // ✅ Ensure role is logged
            'timestamp' => now()->toDateTimeString(),
        ]));

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
                $query->where('status', $request->status); // status column stores "active" or "inactive"
            }
        
            // Apply role filter if provided
            if ($request->has('role') && $request->role !== "") {
                $query->where('role', $request->role);
            }
        
            $totalUsers = $query->count(); // Get total filtered user count
            // Paginate the filtered results
            $users = $query->paginate(7); // Set pagination limit to 7

            Log::info('Filtering by status:', ['status' => $request->status]);
        
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


                event(new ActivityLogged([
                    'action' => 'User ' . $user->first_name . ' ' . $user->last_name . ' was deleted by ' . 
                                auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name . 
                                ' on ' . now()->toDayDateTimeString(),
        
                    'performed_by' => auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
                    'role' => auth()->user()->role, // ✅ Include role of the performing user
                    'timestamp' => now()->toDateTimeString(),
                ]));
                        
                // ✅ Write log to a file
                Log::channel('activity')->info(json_encode([
                    'action' => 'User ' . $user->first_name . ' ' . $user->last_name . ' was deleted by ' . 
                                auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
                    'performed_by' => auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
                    'role' => auth()->user()->role, // ✅ Ensure role is logged
                    'timestamp' => now()->toDateTimeString(),
                ]));


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
            ]);

            // Update the user with only the validated data
            $user->update($validated);


            
            event(new ActivityLogged([
                'action' => 'User ' . $user->first_name . ' ' . $user->last_name . 'account was edited by ' . 
                            auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name . 
                            ' on ' . now()->toDayDateTimeString(),
    
                'performed_by' => auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
                'role' => auth()->user()->role, // ✅ Include role of the performing user
                'timestamp' => now()->toDateTimeString(),
            ]));
                    
            // ✅ Write log to a file
            Log::channel('activity')->info(json_encode([
                'action' => 'User ' . $user->first_name . ' ' . $user->last_name . ' was edited by ' . 
                            auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
                'performed_by' => auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
                'role' => auth()->user()->role, // ✅ Ensure role is logged
                'timestamp' => now()->toDateTimeString(),
            ]));

            // Return response with updated user data
            return response()->json([
                'message' => 'User updated successfully',
                'user' => $user
            ], 200);
        }


        public function getActivityLogs(Request $request)
        {
            $logPath = storage_path('logs/');
            $files = glob($logPath . 'activity-*.log'); 
            
            if (empty($files)) {
                return response()->json([
                    'data' => [],
                    'current_page' => 1,
                    'last_page' => 1
                ]);
            }
        
            $latestLogFile = end($files);
        
            if (!File::exists($latestLogFile)) {
                return response()->json([
                    'data' => [],
                    'current_page' => 1,
                    'last_page' => 1
                ]);
            }
        
            $logs = explode("\n", trim(File::get($latestLogFile))); 
            $logs = array_filter($logs);
        
            $formattedLogs = array_map(function ($log) {
                $jsonStart = strpos($log, '{'); 
                if ($jsonStart === false) return null;
                return json_decode(substr($log, $jsonStart), true);
            }, $logs);
        
            $formattedLogs = array_filter($formattedLogs);
            $formattedLogs = array_reverse($formattedLogs); // Show latest logs first
        
            // ✅ Convert array into a Laravel Collection
            $logCollection = collect($formattedLogs);
        
            // ✅ Apply Filters
            if ($request->has('search')) {
                $search = strtolower($request->input('search'));
                $logCollection = $logCollection->filter(function ($log) use ($search) {
                    return str_contains(strtolower($log['action']), $search) || 
                           str_contains(strtolower($log['performed_by']), $search);
                });
            }
        
            if ($request->has('created_at')) {
                $dateFilter = $request->input('created_at');
                $logCollection = $logCollection->filter(function ($log) use ($dateFilter) {
                    return strpos($log['timestamp'], $dateFilter) !== false;
                });
            }
        
            if ($request->has('role')) {
                $roleFilter = strtolower($request->input('role'));
                $logCollection = $logCollection->filter(function ($log) use ($roleFilter) {
                    return strtolower($log['role']) === $roleFilter;
                });
            }
        
            // ✅ Pagination
            $perPage = 10;
            $page = $request->get('page', 1);
            $offset = ($page - 1) * $perPage;
        
            $paginatedLogs = new LengthAwarePaginator(
                $logCollection->slice($offset, $perPage)->values(),
                $logCollection->count(),
                $perPage,
                $page,
                ['path' => url('/api/activity-logs')]
            );
        
            return response()->json($paginatedLogs);
        }
        



    
}
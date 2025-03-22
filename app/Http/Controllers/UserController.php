<?php

namespace App\Http\Controllers;

use App\Events\ActivityLogged;
use App\Events\AdminNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log; // Import Log facade
use Illuminate\Support\Facades\File; // ✅ Import File facade
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{

    public function changePassword(Request $request){

        //validatre the request from the frontend in v model values
            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|min:8|confirmed',
            ]);

            $user = Auth::user(); // get authenticated user

            // verify if the current password is correct
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json(['messsage' => 'Current password is incorrect'], 400);
            }

            $user->password  = Hash::make($request->new_password);
            /** @var \App\Models\User $user **/
            $user->save();

            return response()->json(['message' => 'Password change Successfully']);
    }


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


        event(new AdminNotification([
            'type' => 'user_created',
            'action' => 'New user ' . $user->first_name . ' ' . $user->last_name . 
                        ' was created by ' . auth()->user()->first_name . ' ' . 
                        (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
        ]));
        

        
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
            $original = $user->getOriginal();
        
            // Validate incoming request data
            $validated = $request->validate([
                'first_name' => 'sometimes|string|max:255',
                'last_name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:users,email,' . $id, // Ensure email is unique except for this user
                'role' => 'required|string',
                'password' => 'nullable|string|min:8|confirmed', // Add password validation
            ]);
        
            // If password is provided, hash it before updating
            if ($request->has('password')) {
                $validated['password'] = bcrypt($request->password);
            }
        
            // Update the user with only the validated data
            $user->update($validated);
        
            $actionMessage = $user->first_name . ' ' . $user->last_name . ' was edited by ' .
            auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name;
        
            if ($original['role'] !== $user->role) {
                $actionMessage .= '. Role changed from ' . $original['role'] . ' to ' . $user->role;
            }
                
            event(new AdminNotification([
                'type' => 'edit_user',
                'action' => $actionMessage,
                'timestamp' => now()->toDateTimeString(),
            ]));
        
            event(new ActivityLogged([
                'action' => 'User ' . $user->first_name . ' ' . $user->last_name . ' account was edited by ' . 
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
        
        public function getAuthUser()
        {
            $user = auth()->user();
        
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
        
            return response()->json([
                'success' => true,
                'data' => $user
            ], 200);
        }
    }
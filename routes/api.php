<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InventoryController;
use App\Http\Resources\UserResource; // Make sure to import the UserResource
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DrController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\RrController;
use App\Http\Controllers\SendResetLinkEmail;
use App\Http\Controllers\storePasswordChangeRequest;
use App\Http\Controllers\SettingController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
// ✅ Add this to authenticate private channels with Sanctum
Broadcast::routes(['middleware' => ['auth:sanctum']]);

// Route for broadcasting authentication
Route::middleware('auth:sanctum')->post('/broadcasting/auth', function (Request $request) {
    // Example: You can access $request if needed, for instance:
    // Check if the user is authenticated
    if ($request->user()) {
        return response()->json(['message' => 'Authorized']);
    }

    return response()->json(['message' => 'Unauthorized'], 401);
});
// Protected route that requires authentication using Sanctum
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    // Return the UserResource to customize the response
    return new UserResource($request->user());
});
// Logout route that requires the user to be authenticated
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// No middleware on login because it handles user authentication
Route::post('login', [AuthController::class, 'login']);





Route::post('/users', [UserController::class, 'store']);
Route::get('/users', [UserController::class, 'index']);
Route::get('/user', [UserController::class, 'getAuthUser']); // get currently log in user
Route::delete('/users/{id}', [UserController::class, 'destroy']);
Route::patch('/users/{id}', [UserController::class, 'update']);
Route::get('/activity-logs', [UserController::class, 'getActivityLogs']);



Route::post('/inventory', [InventoryController::class, 'store']);
Route::get('/inventory', [InventoryController::class, 'index']);
Route::patch('/inventory/{id}', [InventoryController::class, 'update']);
Route::delete('/inventory/{id}', [InventoryController::class, 'destroy']);
Route::get('/inventory/check', [InventoryController::class, 'checkMaterial']);



Route::post('/Dr', [DrController::class, 'store']); // Save DR
Route::get('/Dr', [DrController::class, 'index']);  // Fetch DRs
Route::get('/Dr/{id}', [DrController::class, 'show']);
Route::delete('/Dr/{id}', [DrController::class, 'destroy']);
Route::patch('/Dr/{id}', [DrController::class, 'update']);
Route::get('/drs/archived', [DrController::class, 'archived']);
Route::patch('/drs/{id}/restore', [DrController::class, 'restore']);
Route::delete('/drs/{id}/force-delete', [DrController::class, 'forceDelete']);


Route::get('pdf/generate/{id}', [PDFController::class, 'generatePdf']);
Route::get('pdf/generate/{id}', [PDFController::class, 'generatePDFrr']);

Route::post('/update-dr-Rr', [RrController::class, 'store']); // Save Return
Route::get('/Rr', [RrController::class, 'index']);
Route::delete('/Rr/{id}', [RrController::class, 'destroy']);
Route::get('/Rr/{id}', [RrController::class, 'show']);
Route::post('/updateReturnReceipt/{id}', [RrController::class, 'update']);
Route::get('/rrs/archived', [RrController::class, 'archived']);
Route::patch('/rrs/{id}/restore', [RrController::class, 'restore']);
Route::delete('/rrs/{id}/force-delete', [RrController::class, 'forceDelete']);


Route::post('forgot-password', [SendResetLinkEmail::class, 'sendResetLinkEmail']);

Route::patch('/password-change/{id}', [UserController::class, 'changePassword']);

Route::get('/settings/threshold', [SettingController::class, 'getThreshold']);
Route::post('/settings/threshold', [SettingController::class, 'updateThreshold']);
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Resources\UserResource; // Make sure to import the UserResource
use App\Http\Controllers\UserController;
use App\Http\Controllers\DrController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\RrController;
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
// Protected route that requires authentication using Sanctum
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    // Return the UserResource to customize the response
    return new UserResource($request->user());
});
// Logout route that requires the user to be authenticated
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// No middleware on login because it handles user authentication
Route::post('login', [AuthController::class, 'login']);


Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);


Route::post('/users', [UserController::class, 'store']);
Route::get('/users', [UserController::class, 'index']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);
Route::patch('/users/{id}', [UserController::class, 'update']);

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


Route::get('pdf/generate/{id}', [PDFController::class, 'generatePdf']);
Route::get('pdf/generate/{id}', [PDFController::class, 'generatePDFrr']);

Route::post('/update-dr-Rr', [RrController::class, 'store']); // Save Return
Route::get('/Rr', [RrController::class, 'index']);
Route::delete('/Rr/{id}', [RrController::class, 'destroy']);
Route::get('/Rr/{id}', [RrController::class, 'show']);
Route::post('/updateReturnReceipt/{id}', [RrController::class, 'update']);
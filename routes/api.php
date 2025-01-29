<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Resources\UserResource; // Make sure to import the UserResource

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
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SendResetLinkEmail;

// Show the form to request a password reset link
Route::get('forgot-password', [SendResetLinkEmail::class, 'showLinkRequestForm'])->name('password.request');

// Send the password reset link to the provided email
Route::post('forgot-password', [SendResetLinkEmail::class, 'sendResetLinkEmail'])->name('password.email');

// Show the form to reset the password (with token)
Route::get('reset-password/{token}', [SendResetLinkEmail::class, 'showResetForm'])->name('password.reset');

// Handle the password reset
Route::post('reset-password', [SendResetLinkEmail::class, 'reset'])->name('password.update');

// Exclude /api routes from the catch-all
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '^(?!api).*$'); // Excludes paths starting with "api"


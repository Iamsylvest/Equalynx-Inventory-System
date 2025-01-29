<?php

use Illuminate\Support\Facades\Route;

// Exclude /api routes from the catch-all
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '^(?!api).*$'); // Excludes paths starting with "api"
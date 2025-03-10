<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/
Broadcast::channel('user-activity', function () {
    return Auth::check(); // ✅ Allow authenticated users
});

Broadcast::channel('activity-logs', function ($user) {
    return $user->role ==='admin'; // ✅ Only allow admins
});
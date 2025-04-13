<?php

use Illuminate\Broadcasting\Channel;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;
use illuminate\Support\Facades\Log;
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

Broadcast::channel('warehouse-notification', function ($user) {
    return in_array($user->role, ['warehouse_staff', 'manager']); // Allow both roles
});

Broadcast::channel('admin-notification', function ($user){
    return $user->role === 'admin'; // ✅ Only allow admins
});


Broadcast::channel('procurement-notification', function ($user){
    return $user->role === 'procurement'; // ✅ Only allow admins
});

Broadcast::channel('broadcast-high-stock', function ($user) {
    return $user->role === 'procurement';
});

Broadcast::channel('low-stock-dashboard', function ($user) {
    Log::info('Authorizing user:', ['user' => $user]);
    return $user && $user->role === 'procurement';
});

Broadcast::channel('broadcast-out-stock', function ($user) {
    return $user->role === 'procurement';
});

Broadcast::channel('broadcast-slow-moving', function ($user) {
    return $user->role === 'procurement';
});

Broadcast::channel('broadcast-bargraph', function ($user) {
    return $user->role === 'procurement';
});

Broadcast::channel('broadcast-linegraph', function ($user) {
    return $user->role === 'procurement';
});


Broadcast::channel('manager-notification', function ($user){
    return $user->role === 'manager'; // ✅ Only allow admins
});

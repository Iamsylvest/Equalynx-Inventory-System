<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserLoggedIn implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }
    // Define the broadcast channel (Private means only authenticated users can listen)
    public function broadcastOn()
    {
        return new PrivateChannel('user-activity'); // Now it's a private channel
    }
    // Define what data is sent with the event
    public function broadcastWith()
    {
        return [
            'id' => $this->user->id,
            'name' => $this->user->name,
            'status' => 'active' // Make sure status is correct
        ];
    }
}

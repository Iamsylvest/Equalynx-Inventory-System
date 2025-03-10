<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ActivityLogged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $log;
   
    public function __construct($log)
    {
       $this->log = $log;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('activity-logs'); 
    }
    public function broadcastAs(){
        return 'log-updated';
    }
    public function broadcastWith(){
       return[
        'action' => $this->log['action'] ?? 'No action provided',
        'performed_by' => $this->log['performed_by'] ?? 'Unknown',
        'role' => $this->log['role'] ?? 'Unknown', // ✅ Ensure role is included
        'timestamp' => $this->log['timestamp'] ?? now()->toDateTimeString(),
       ];
    }
}

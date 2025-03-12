<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log; // Import Log facade

class WarehouseNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notif;

    public function __construct($notif)
    {
       $this->notif = $notif;
    }
    public function broadcastOn()
    {
        $user = auth()->user(); // Get the authenticated user
    
        // Check if the user is admin or warehouse staff
        if ($user && ($user->role === 'warehouse_staff' || $user->role === 'admin')) {
            return new PrivateChannel('warehouse-notification');
        }
    
        // If the user is not authorized, you can return an empty array or different logic
        return [];
    }
    public function broadcastAs(){
        return 'notified-warehouse'; // ✅ Match event name
    }
    public function broadcastWith(){

        Log::info('Broadcasting data:', $this->notif);

        if($this->notif['type'] === 'low_stock'){
            return [
                'action' => $this->notif['action'] ?? 'No notification',
                'timestamp' => now()->timestamp * 1000, // ✅ JS-compatible timestamp
            ];
        }
        elseif ($this->notif['type'] === 'high_stock') {
              return [
                'action' => $this->notif['action'] ?? 'No notification',
                'timestamp' => now()->timestamp * 1000, // ✅ JS-compatible timestamp
              ];
        }

        return [];
    }
}

<?php

namespace App\Events;


use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log; // Import Log facade
use Illuminate\Support\Str; // ✅ Import Str for generating UUID

class AdminNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notif;
    public $id;
   
    public function __construct($notif)
    {
      $this->notif = $notif;
      $this->id = Str::uuid()->toString(); // ✅ Fixed casing
    }

   
    public function broadcastOn()
    {
        return new PrivateChannel('admin-notification');
    }
    public function broadcastAs(){
        return 'notified-admin';
    }
    public function broadcastWith()
    {
        // Check if the notification type exists in the allowed types
        $allowedTypes = ['user_created', 'edit_user', 'new_material', 'low_stock', 'high_stock', 'new_dr', 'approved_dr', 'access_invalid', 'approved_rr'];
        
        // If the type is one of the allowed types, return the notification structure
        if (in_array($this->notif['type'], $allowedTypes)) {
            return [
                'id' => $this->id,
                'action' => $this->notif['action'] ?? 'No notification',
                'timestamp' => now()->timestamp * 1000,
            ];
        }
    
        // Return an empty array for any other notification types
        return [];
    }
}

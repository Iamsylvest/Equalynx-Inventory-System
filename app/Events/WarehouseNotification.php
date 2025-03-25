<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log; // Import Log facade
use Illuminate\Support\Str; // ✅ Import Str for generating UUID

class WarehouseNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notif;
    public $id;

    public function __construct($notif)
    {
       $this->notif = $notif;
       $this->id = str::uuid()->toString(); // Generate unique ID
    }
    public function broadcastOn()
    {
        return new PrivateChannel('warehouse-notification');
    }
    public function broadcastAs(){
        
        return 'notified-warehouse'; // ✅ Match event name
    }
    public function broadcastWith()
    {
        // List of allowed types
        $allowedTypes = [
            'low_stock', 'high_stock', 'edit', 'rr_created', 'approved', 'new_material', 'Threshold_Updated'
        ];
        
        // Check if the notification type is in the allowed types
        if (in_array($this->notif['type'], $allowedTypes)) {
            return [
                'id' => $this->id, // include unique ID
                'action' => $this->notif['action'] ?? 'No notification',
                'timestamp' => now()->timestamp * 1000, // ✅ JS-compatible timestamp
            ];
        }
    
        // Return empty array for unsupported types
        return [];
    }
}

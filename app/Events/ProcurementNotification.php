<?php

namespace App\Events;


use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str; // ✅ Import Str for generating UUID

class ProcurementNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notif;
    public $id;

    public function __construct($notif)
    {
       $this->notif = $notif;
       $this->id = Str::uuid()->toString(); // Unique id for notication
    }

    public function broadcastOn()
    {
        return new PrivateChannel('procurement-notification');
    }

    public function broadcastAs(){
        return 'notified-procurement';
    }

    public function broadcastWith(){
        $allowed_types =  ['approved_dr', 'approved_rr'];

            // If the type is one of the allowed types, return the notification structure
        if (in_array($this->notif['type'], $allowed_types)) {
            return [
                'id' => $this->id, // include unique ID
                'action' => $this->notif['action'] ?? 'No notification',
                'timestamp' => now()->timestamp * 1000, // ✅ JS-compatible timestamp
            ];
        }
          // Return an empty array for any other notification types
          return null;

    }
}

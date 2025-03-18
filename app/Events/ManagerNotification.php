<?php

namespace App\Events;


use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str; // ✅ Import Str for generating UUID

class ManagerNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notif;
    public $id;

    public function __construct($notif)
    {
        $this->notif = $notif;
        $this->id = Str::uuid()->toString(); // unique id for notif
    }

    public function broadcastOn()
    {
        return new PrivateChannel('manager-notification');
    }
    public function broadcastAs(){
        return 'notified-manager';
    }
    public function broadcastWith(){
        $allowed_types = ['new_dr', 'approved_dr', 'rr_created', 'approved_rr'];

        if (in_array($this->notif['type'], $allowed_types)) {
            return[
                'id' => $this->id, // include unique ID
                'action' => $this->notif['action'] ?? 'No notification',
                'timestamp' => now()->timestamp * 1000, // ✅ JS-compatible timestamp
            ];
        }

        return null;
    }

}

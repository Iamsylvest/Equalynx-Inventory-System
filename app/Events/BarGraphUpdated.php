<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BarGraphUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $extraInfo;
    public $inventoryMaterials;
    

    public function __construct($inventoryMaterials, $extraInfo)
    {
      $this->inventoryMaterials = $inventoryMaterials;
      $this->extraInfo = $extraInfo;
    }


    public function broadcastOn()
    {
        return new PrivateChannel('broadcast-bargraph');
    }
    public function broadcastAs()
    {
        return 'bargraph-updated';
    }

    public function broadcastWith()
    {
            return [
                'inventoryMaterials' => $this->inventoryMaterials,
                'extraInfo' => $this->extraInfo,
            ];
    }
}

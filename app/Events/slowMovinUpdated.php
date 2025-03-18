<?php

namespace App\Events;


use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class slowMovinUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $slowMovingMaterials;
    public $totalSlowMoving;
    public $extraInfo;


    public function __construct($slowMovingMaterials, $totalSlowMoving, $extraInfo)
    {
        $this->slowMovingMaterials = $slowMovingMaterials;
        $this->totalSlowMoving = $totalSlowMoving;
        $this->extraInfo = $extraInfo;
    }
    public function broadcastOn()
    {
        return new PrivateChannel('broadcast-slow-moving');
    }
    public function broadcastAs(){
        return 'slow-moving'; 
    }
    public function broadcastWith(){
        return [
            'slowMovingMaterials' => $this->slowMovingMaterials,
            'totalSlowMoving' => $this->totalSlowMoving,
            'extraInfo' => $this->extraInfo,
        ];
    }
}

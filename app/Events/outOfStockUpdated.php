<?php

namespace App\Events;


use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class outOfStockUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $outOfStock;
    public $totalOutStock;
    public $extraInfo;

    public function __construct($outOfStock, $totalOutStock, $extraInfo = [])
    {
      $this->outOfStock = $outOfStock;
      $this->totalOutStock = $totalOutStock;
      $this->extraInfo = $extraInfo;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('broadcast-out-stock');
    }
    public function broadcastAs(){
        return 'outStock';
    }
    public function broadcastWith(){
        return[
            'totalOutStock' => $this->totalOutStock,
            'outOfStock' => $this->outOfStock,
            'extraInfo' => $this->extraInfo,
        ];

    }
}

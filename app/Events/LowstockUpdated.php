<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LowstockUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $totalLowStock;
    public $lowStockMaterials;
    public $extraInfo;
    
    public function __construct($totalLowStock, $lowStockMaterials, $extraInfo = [])
    {
      $this->totalLowStock = $totalLowStock;
      $this->lowStockMaterials = $lowStockMaterials;
      $this->extraInfo = $extraInfo;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('low-stock-dashboard');
    }
    public function broadcastAs(){
         return 'lowStockUpdated'; // ✅ This is the event name used in the frontend!
    }
    public function broadcastWith()
{
    return [
        'totalLowStock' => $this->totalLowStock,
        'lowStockMaterials' => $this->lowStockMaterials,
        'extraInfo' => $this->extraInfo,
    ];
}
   
}

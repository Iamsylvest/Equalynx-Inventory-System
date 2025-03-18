<?php

namespace App\Events;


use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use illuminate\Support\Facades\Log;
class highStockUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $highStock;
    public $totalMaterials;
    public $extraInfo;

    public function __construct($highStock, $totalMaterials, $extraInfo = [])
    {
        $this->highStock = $highStock;
        $this->totalMaterials = $totalMaterials;
        $this->extraInfo = $extraInfo;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('broadcast-high-stock');
    }
    public function broadcastAs(){
        return 'highStockUpdate';
    }
    public function broadcastWith()
    {
           // Log the broadcast data before returning
                Log::info('Broadcasting high stock data:', [
                    'highStock' => $this->highStock,
                    'totalMaterials' => $this->totalMaterials,
                    'extraInfo' => $this->extraInfo,
                ]);
            return[
                'highStock' => $this->highStock,
                'totalMaterials' => $this->totalMaterials,
                'extraInfo' => $this->extraInfo,
            ];
         
    }
}

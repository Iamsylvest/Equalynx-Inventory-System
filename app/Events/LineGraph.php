<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LineGraph implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $inventoryMaterials;
    public $extraInfo;
  
    public function __construct($inventoryMaterials, $extraInfo)
    {
        $this->inventoryMaterials = $inventoryMaterials;
        $this->extraInfo = $extraInfo; // ✅ Fixed assignment
    }

    public function broadcastOn()
    {
        return new PrivateChannel('broadcast-linegraph');
    }
    public function broadcastAs()
    {
        return 'linegraph-updated';
    }
    public function broadcastWith()
    {
        return [
            'inventoryMaterials' => $this->inventoryMaterials->map(function ($material) {
                return [
                    'material_name' => $material->material_name,
                    'measurement_quantity' => $material->measurement_quantity,
                    'measurement_unit' => $material->measurement_unit,
                    'stocks' => $material->stocks,
                    'date' => $material->created_at->format('Y-m-d H:i') // ✅ Format created_at for display
                ];
            }),
            'extraInfo' => $this->extraInfo,
        ];
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Inventory;
use Carbon\Carbon;
use App\Events\slowMovinUpdated;
class BroadcastSlowMoving extends Command
{
    

  // commans name for scheduling

    protected $signature = 'broadcast:slow-moving';
    protected $description = 'Broadcast slow-moving materials';


    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
         // ✅ Define the threshold for slow-moving materials (e.g., not updated in the last 15 days)
         $thresholdDate = Carbon::now()->subMinutes(2); // Subtract 2 minutes for quick testing

        // get all slow moving materials
        $slowMovingMaterilas = Inventory::where('updated_at', '<', $thresholdDate)->get();
        $totalSlowMoving = $slowMovingMaterilas->count();

        $extraInfo = [];
        foreach ($slowMovingMaterilas as $material) {
            $extraInfo[] = [
                    'type' => 'slow_moving',
                    'action' => $material->material_name . ' has not been updated since ' . $material->updated_at->toDateTimeString(), 
                    'timestamp' => $material->updated_at->toDateTimeString(),
            ];
        }

       event(new slowMovinUpdated($slowMovingMaterilas, $totalSlowMoving, $extraInfo));

       $this->info("Successfully broadcasted $totalSlowMoving slow-moving materials");
    }
}

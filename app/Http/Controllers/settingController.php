<?php

namespace App\Http\Controllers;

use App\Helpers\SettingsHelper;
use Illuminate\Http\Request;
use App\Events\AdminNotification;
use App\Events\WarehouseNotification;
class settingController extends Controller
{
    

   public function getThreshold(){
        return response()->json(['threshold' => SettingsHelper::getThreshold()]);

   }

   //update the threshold (Admin Only)
   public function updateThreshold(Request $request){
        $request->validate([
            'threshold' => 'required|integer|min:1' // Ensure a valid value

        ]);


        // use helper function to update the threshold
        SettingsHelper::setThreshold($request->threshold);

        event(new AdminNotification([
          'type' => 'Threshold_Updated',
          'action' => 'The threshold has been updated to ' . $request->threshold,
          'timestamp' => now()->toDateTimeLocalString(),
      ]));

      event(new WarehouseNotification([
          'type' => 'Threshold_Updated',
          'action' => 'The threshold has been updated to ' . $request->threshold,
          'timestamp' => now()->toDateTimeLocalString(),
      ]));


        return response()->json(['message' => 'Threshold update successfully']);
   }
}

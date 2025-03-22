<?php

namespace App\Http\Controllers;

use App\Helpers\SettingsHelper;
use Illuminate\Http\Request;

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
        return response()->json(['message' => 'Threshold update successfully']);
   }
}

<?php

namespace App\Helpers;

use App\Models\Setting;

class SettingsHelper
{
    // Get the threshold value
    public static function getThreshold()
    {
        return Setting::where('key', 'threshold')->value('value') ?? 20;
    }

    // Set a new threshold value
    public static function setThreshold($value)
    {
        Setting::updateOrCreate(
            ['key' => 'threshold'],
            ['value' => $value]
        );
    }
}
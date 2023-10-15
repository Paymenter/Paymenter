<?php

namespace App\Classes;

use App\Models\Setting;

class Routing
{

    /* 
    * Returns true if current theme wants to use Laravel's routing
    */
    public static function useLaravelRouting()
    {
        $themeName = config('settings::theme-active') ?? 'default';

        try {
            $themeData = file_get_contents(base_path() . "/themes/{$themeName}/theme.json");
            $themeConfig = json_decode($themeData, true);
        } catch (\Exception $e) {
            throw new \Exception("Unable to read current theme's config file", 500, $e);
        }

        return $themeConfig['laravelRouting'] ?? true;
    }
}

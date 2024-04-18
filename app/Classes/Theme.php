<?php
namespace App\Classes;

class Theme
{
    public static function getSettings(){
        try {
            $theme = require base_path('themes/'.config('settings.theme').'/theme.php');
        } catch (\Throwable $th) {
            throw new \Exception('Theme file could not be read.');
        }

        // Add theme settings prefix to name <theme_name>_<setting_name>
        $settings = [];
        foreach ($theme['settings'] as $setting) {
            $setting['name'] = 'theme_' . config('settings.theme').'_'.$setting['name'];
            $settings[] = $setting;
        }

        return $settings;
    }
}
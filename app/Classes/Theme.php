<?php

namespace App\Classes;

use Exception;
use Throwable;

class Theme
{
    public static function getSettings()
    {
        try {
            $theme = require base_path('themes/' . config('settings.theme', 'default') . '/theme.php');
        } catch (Throwable $th) {
            // If not ran from the command line, throw an exception
            if (php_sapi_name() !== 'cli') {
                throw new Exception('Theme file could not be read. ' . $th);
            } else {
                return [];
            }
        }

        // Add theme settings prefix to name <theme_name>_<setting_name>
        $settings = [];
        foreach ($theme['settings'] as $setting) {
            $setting['name'] = 'theme_' . config('settings.theme', 'default') . '_' . $setting['name'];
            $settings[] = $setting;
        }

        return $settings;
    }
}

<?php

use App\Helpers\EventHelper;
use Illuminate\Config\Repository;

if (!function_exists('theme')) {
    /**
     * Get the specified configuration value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param  array|string  $key
     * @param  mixed  $default
     * @return mixed|Repository
     */
    function theme($key, $default = null)
    {
        $current_theme = config('settings.theme', 'default');

        return config("settings.theme_$current_theme" . "_$key", $default) ?? $default;
    }
}

if (!function_exists('hook')) {
    /**
     * Dispatch an event and return the items
     *
     * @param  string  $event
     * @param  array  $items
     * @return array
     */
    function hook($event)
    {
        return EventHelper::renderEvent($event);
    }
}

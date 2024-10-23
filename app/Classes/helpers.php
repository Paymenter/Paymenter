<?php

if (!function_exists('theme')) {
    /**
     * Get the specified configuration value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param  array|string  $key
     * @param  mixed  $default
     * @return mixed|\Illuminate\Config\Repository
     */
    function theme($key, $default = null)
    {
        return config('settings.theme_' . config('settings.theme', 'default') . '_' . $key, $default);
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
        return \App\Helpers\EventHelper::renderEvent($event);
    }
}

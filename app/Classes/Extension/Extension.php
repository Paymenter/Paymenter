<?php

namespace App\Classes\Extension;

use App\Models\Gateway;

/**
 * Base class for extensions
 *
 * @link https://docs.paymenter.org/development/extensions
 */
class Extension
{
    public function __construct(public $config = []) {}

    /**
     * Get a configuration value
     *
     * @param  string  $key
     * @return mixed
     */
    public function config($key)
    {
        if (empty($this->config)) {
            $this->config = Gateway::where('extension', class_basename(static::class))->first()->settings->pluck('value', 'key')->toArray();
        }

        return $this->config[$key] ?? null;
    }

    /**
     * Get the configuration fields for the extension
     *
     * @link https://docs.paymenter.org
     *
     * @param  array  $values  The current values of the configuration (is empty on first load)
     * @return array
     */
    public function getConfig($values = [])
    {
        return [];
    }

    /**
     * Get the meta data for the extension
     * E.g. name, description, version
     *
     * @example return [
     *     'name' => 'Paymenter',
     *     'description' => 'Manage and optimize your hosting business with Paymenter',
     * ]
     *
     * @link https://docs.paymenter.org
     *
     * @return array
     */
    public function getMeta()
    {
        return [];
    }
}

<?php

namespace App\Classes\Extension;

use App\Models\Extension as ModelsExtension;
use App\Models\Gateway;
use App\Models\Server;

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
            // Check from which type its being called
            $type = debug_backtrace()[1]['class'];
            $type = str_replace('Paymenter\Extensions\\', '', $type);
            $type = str_replace('\\' . class_basename(static::class), '', $type);
            if (in_array($type, ['Servers', 'Gateways'])) {
                $type = substr($type, 0, -1);
                $type = ($type == 'Gateway') ? Gateway::class : Server::class;
                $this->config = $type::where('extension', class_basename(static::class))->first()->settings->pluck('value', 'key')->toArray();
            } else {
                $this->config = ModelsExtension::where('extension', class_basename(static::class))->first()->settings->pluck('value', 'key')->toArray();
            }
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
    public function getMetadata()
    {
        return [];
    }
}

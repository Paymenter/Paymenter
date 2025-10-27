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
     * Called when the extension is installed for the first time
     * If the extension type is server or gateway, it will be called when the first server or gateway is created
     *
     * @return void
     */
    public function installed() {}

    /**
     * Called when the extension is uninstalled
     * If the extension type is server or gateway, it will be called when the last server or gateway is deleted
     *
     * @return void
     */
    public function uninstalled() {}

    /**
     * Called when the extension is updated
     * This is called when the extension is updated to a new version
     *
     * @param  string  $oldVersion  The old version of the extension
     * @return void
     */
    public function upgraded($oldVersion = null) {}

    /**
     * Called every request to the extension (if the extension is enabled)
     *
     * @return void
     */
    public function boot() {}
}

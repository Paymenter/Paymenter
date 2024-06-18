<?php

namespace App\Helpers;

use App\Classes\FilamentInput;
use Exception;

class ExtensionHelper
{
    /**
     * Used to read all Extensions in app/Extensions with or without type (e.g. 'gateway' or 'server')
     * 
     * @param string|null $type
     * @return array
     */
    private static function getExtensions($type)
    {
        // Read app/Extensions directory
        $availableExtensions = array_diff(scandir(app_path('Extensions/' . ucfirst($type . 's'))), ['..', '.']);

        // Read settings
        foreach ($availableExtensions as $key => $extension) {
            $extensions[] = [
                'name' => $extension,
                'settings' => self::getConfig($type, $extension)
            ];
        }

        return $extensions;
    }

    /**
     * Get extension and return new instance
     * 
     * @param string $type
     * @param string $extension
     * @return object
     */
    public static function getExtension($type, $extension, $config = [])
    {
        $extension = '\\App\\Extensions\\' . ucfirst($type) . 's\\' . $extension . '\\' . $extension;

        if (!class_exists($extension)) {
            throw new Exception('Extension not found');
        }

        return new $extension($config);
    }

    /**
     * Get available settings
     * 
     * @return array
     */
    public static function getConfig($type, $extension)
    {
        return self::getExtension($type, $extension)->getConfig();
    }

    /**
     * Has function
     * 
     * @param object $extension
     * @param string $function
     */
    public static function hasFunction($extension, $function)
    {
        return method_exists(self::getExtension($extension->type, $extension->extension, $extension->settings), $function);
    }

    /**
     * Test connection
     * 
     * @return string
     */
    public static function testConfig($extension, $values)
    {
        return self::getExtension($extension->type, $extension->extension, $values)->testConfig();
    }

    /**
     * Get available gateways
     * 
     * @return array
     */
    public static function getAvailableGateways()
    {
        return self::getExtensions('gateway');
    }

    /**
     * Get available servers
     * 
     * @return array
     */
    public static function getAvailableServers()
    {
        return self::getExtensions('server');
    }

    /**
     * Convert extensions to options
     * 
     * @param array $extensions
     * @return object
     */
    public static function convertToOptions($extensions)
    {
        $options = [];
        $settings = ['default' => []];
        foreach ($extensions as $extension) {
            $options[$extension['name']] = $extension['name'];
            foreach ($extension['settings'] as $setting) {
                $setting['name'] = 'settings.' . $setting['name'];
                $settings[$extension['name']][] = FilamentInput::convert($setting, true);
            }
        }
        return (object) ['options' => $options, 'settings' => $settings];
    }

    /**
     * Get available settings
     * 
     * @return array
     */
    public static function getProductConfig($server, $values = [])
    {
        return self::getExtension('server', $server->extension, self::settingsToArray($server->settings))->getProductConfig($values);
    }

    public static function settingsToArray($settings)
    {
        $settingsArray = [];

        if ($settings instanceof \Illuminate\Database\Eloquent\Collection) {
            // If $settings is a collection of models
            foreach ($settings as $setting) {
                $settingsArray[$setting->key] = $setting->value;
            }
        } elseif ($settings instanceof \Illuminate\Database\Eloquent\Model) {
            // If $settings is a single model
            $settingsArray[$settings->name] = $settings->value;
        }

        return $settingsArray ?? $settings;
    }
}

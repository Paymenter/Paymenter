<?php

namespace App\Helpers;

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
                'settings' => self::getAvailableConfig($type, $extension)
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
    public static function getExtension($type, $extension)
    {
        $extension = '\\App\\Extensions\\' . ucfirst($type) . 's\\' . $extension . '\\' . $extension;

        if (!class_exists($extension)) {
            throw new Exception('Extension not found');
        }

        return new $extension;
    }

    /**
     * Get available settings
     * 
     * @return array
     */
    public static function getAvailableConfig($type, $extension)
    {
        return self::getExtension($type, $extension)->getConfig();
    }

    public static function getAvailableGateways()
    {
        return self::getExtensions('gateway');
    }
}

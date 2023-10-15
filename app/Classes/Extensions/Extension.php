<?php

namespace App\Classes\Extensions;

use App\Models\Extension as ModelsExtension;

class Extension
{
    /**
     * The extension model
     * 
     * @var Extension
     */
    public $extension;

    public function __construct(ModelsExtension $extension)
    {
        $this->extension = $extension;
    }

    /**
     * Returns metadata about the extension
     * 
     * @return array
     */
    public function getMetadata()
    {
        return [
            'display_name' => null,
            'version' => null,
            'author' => null,
            'website' => null,
        ];
    }

    /**
     * Get all the configuration for the extension
     * 
     * @return array
     */
    public function getConfig()
    {
        return [];
    }
}

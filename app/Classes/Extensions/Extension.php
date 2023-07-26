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
    private $extension;

    public function __construct(ModelsExtension $extension)
    {
        $this->extension = $extension;
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

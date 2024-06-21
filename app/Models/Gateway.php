<?php

namespace App\Models;

class Gateway extends Extension
{
    protected $table = 'extensions';

    public function newQuery($excludeDeleted = true)
    {
        return parent::newQuery($excludeDeleted)->where('type', 'gateway');
    }
}

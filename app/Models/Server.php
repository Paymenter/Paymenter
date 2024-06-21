<?php

namespace App\Models;

class Server extends Extension
{
    protected $table = 'extensions';

    public function newQuery($excludeDeleted = true)
    {
        return parent::newQuery($excludeDeleted)->where('type', 'server');
    }
}

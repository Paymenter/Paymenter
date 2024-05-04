<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gateway extends Extension
{
    protected $table = 'extensions';

    public function newQuery($excludeDeleted = true)
    {
        return parent::newQuery($excludeDeleted)->where('type', 'gateway');
    }
}

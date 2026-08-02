<?php

namespace Paymenter\Extensions\Others\CyberpunkTheme\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $table = 'ext_cyberpunk_visits';

    protected $guarded = [];

    protected $casts = [
        'day' => 'date',
    ];
}

<?php

namespace App\Models;

class DebugLog extends Model
{
    protected $fillable = [
        'context',
        'type',
    ];

    protected $casts = [
        'context' => 'json',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

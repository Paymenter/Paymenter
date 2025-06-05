<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
    protected $fillable = [
        'name',
        'permissions',
        'token',
        'user_id',
        'type',
        'ip_addresses',
        'last_used_at',
        'enabled',
    ];

    protected $casts = [
        'permissions' => 'array',
        'ip_addresses' => 'array',
        'last_used_at' => 'datetime',
    ];
}

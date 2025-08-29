<?php

namespace App\Models;

use OwenIt\Auditing\Contracts\Auditable;

class ApiKey extends Model implements Auditable
{
    use \App\Models\Traits\Auditable;

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

    protected $auditExclude = [
        'last_used_at',
    ];
}

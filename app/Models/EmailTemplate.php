<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class EmailTemplate extends Model implements Auditable
{
    use \App\Models\Traits\Auditable, HasFactory;

    protected $fillable = [
        'key',
        'name',
        'subject',
        'enabled',
        'body',
        'cc',
        'bcc',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'cc' => 'array',
        'bcc' => 'array',
    ];
}

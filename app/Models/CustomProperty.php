<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class CustomProperty extends Model implements Auditable
{
    use \App\Models\Traits\Auditable, HasFactory;

    public $timestamps = false;

    public $guarded = [];

    public $casts = [
        'allowed_values' => 'array',
    ];
}

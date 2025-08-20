<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class CustomProperty extends Model implements Auditable
{
    use HasFactory, \App\Models\Traits\Auditable;

    public $timestamps = false;

    public $guarded = [];

    public $casts = [
        'allowed_values' => 'array',
    ];
}

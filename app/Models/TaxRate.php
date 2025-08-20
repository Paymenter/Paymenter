<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class TaxRate extends Model implements Auditable
{
    use HasFactory, \App\Models\Traits\Auditable;

    protected $fillable = ['name', 'rate', 'country'];
}

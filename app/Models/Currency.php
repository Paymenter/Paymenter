<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    public $timestamps = false;

    public $incrementing = false;

    protected $keyType = 'string';

    use HasFactory;
}

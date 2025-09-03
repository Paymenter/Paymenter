<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Currency extends Model
{
    use HasFactory;

    public $timestamps = false;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $primaryKey = 'code';

    protected $fillable = [
        'code',
        'name',
        'prefix',
        'suffix',
        'format',
    ];
}

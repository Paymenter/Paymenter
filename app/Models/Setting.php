<?php

namespace App\Models;

use App\Events\Setting\Retrieved;
use App\Events\Setting\Saved;
use App\Events\Setting\Saving;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'value',
        'type',
        'encrypted',
    ];

    protected $dispatchesEvents = [
        'retrieved' => Retrieved::class,
        'saving' => Saving::class,
        'saved' => Saved::class,
    ];
}

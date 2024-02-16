<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Price extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $guarded = [];

    public function priceable(): MorphTo
    {
        return $this->morphTo();
    }
}

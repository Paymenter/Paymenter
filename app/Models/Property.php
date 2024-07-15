<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    public $guarded = [];

    public function parent_property()
    {
        return $this->belongsTo(CustomProperty::class, 'custom_property_id');
    }
}

<?php

namespace App\Models;

use App\Observers\PropertyObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

#[ObservedBy([PropertyObserver::class])]
class Property extends Model implements Auditable
{
    use \App\Models\Traits\Auditable, HasFactory;

    public $guarded = [];

    public function parent_property()
    {
        return $this->belongsTo(CustomProperty::class, 'custom_property_id');
    }

    public function model()
    {
        return $this->morphTo();
    }
}

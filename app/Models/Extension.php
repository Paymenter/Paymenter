<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Extension extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'enabled',
        // Name of extension class (e.g. 'Stripe' or 'Paypal')
        'extension',
        'type',
    ];

    protected $guarded = [];

    /**
     * Get the extension's settings.
     */
    public function settings()
    {
        return $this->morphMany(Setting::class, 'settingable');
    }

    public function path(): Attribute
    {
        return Attribute::make(
            get: fn () => ucfirst($this->type) . 's/' . ucfirst($this->extension)
        );
    }

    public function namespace(): Attribute
    {
        return Attribute::make(
            get: fn () => 'Paymenter\\Extensions\\' . ucfirst($this->type) . 's\\' . ucfirst($this->extension)
        );
    }
}

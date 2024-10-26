<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;

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

    // Listen for created, updated, deleted events and call the boot method
    protected static function boot()
    {
        parent::boot();

        static::created(function ($extension) {
            try {
                Artisan::call('app:optimize');
            } catch (\Exception $e) {
                // Fail silently
            }
        });

        static::updated(function ($extension) {
            try {
                Artisan::call('app:optimize');
            } catch (\Exception $e) {
                // Fail silently
            }
        });

        static::deleted(function ($extension) {
            try {
                Artisan::call('app:optimize');
            } catch (\Exception $e) {
                // Fail silently
            }
        });
    }

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
            get: fn() => ucfirst($this->type) . 's/' . ucfirst($this->extension)
        );
    }

    public function namespace(): Attribute
    {
        return Attribute::make(
            get: fn() => 'Paymenter\\Extensions\\' . ucfirst($this->type) . 's\\' . ucfirst($this->extension)
        );
    }
}

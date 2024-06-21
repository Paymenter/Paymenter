<?php

namespace App\Models;

use App\Models\Traits\Encrypted;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use Encrypted, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'value',
        'type',
    ];

    // Listen for boot event
    protected static function boot()
    {
        parent::boot();

        // When creating a new setting, encrypt the value
        static::creating(function ($setting) {
            if ($setting->encrypted) {
                $setting->value = encrypt($setting->value);
            }

            switch ($setting->type) {
                case 'boolean':
                    $setting->value = (bool) $setting->value;
                case 'integer':
                    $setting->value = (int) $setting->value;
                case 'float':
                    $setting->value = (float) $setting->value;
                case 'array':
                    $setting->value = json_encode($setting->value);
                default:
                    return;
            }
        });

        // When retrieving a setting, decrypt the value
        static::retrieved(function ($setting) {
            if ($setting->encrypted) {
                $setting->value = decrypt($setting->value);
            }

            switch ($setting->type) {
                case 'boolean':
                    $setting->value = (bool) $setting->value;
                case 'integer':
                    $setting->value = (int) $setting->value;
                case 'float':
                    $setting->value = (float) $setting->value;
                case 'array':
                    $setting->value = json_decode($setting->value, true);
                default:
                    return;
            }
        });

        static::updated(function ($setting) {
            \App\Providers\SettingsProvider::flushCache();
        });
    }
}

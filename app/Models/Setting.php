<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;
    protected $table = 'settings';
    protected $primaryKey = 'key';
    public $incrementing = false;

    protected $fillable = [
        'key',
        'value',
    ];

    // After changing settings, clear cache
    public static function boot()
    {
        parent::boot();
        static::saved(function ($setting) {
            Cache::forget('settings::' . $setting->key);
        });
    }
}

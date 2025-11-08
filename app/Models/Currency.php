<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

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

    public function newEloquentBuilder($query)
    {
        return new Builders\CacheableBuilder($query);
    }

    // Clear cache when model is updated, created, or deleted
    protected static function booted()
    {
        // Currencies change infrequently, so we can clear the entire cache on changes
        static::created(function ($currency) {
            Cache::flush();
        });

        static::saved(function ($currency) {
            Cache::flush();
        });

        static::deleted(function ($currency) {
            Cache::flush();
        });
    }

    public function services()
    {
        return $this->hasMany(Service::class, 'currency_code', 'code');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'currency_code', 'code');
    }

    public function credits()
    {
        return $this->hasMany(Credit::class, 'currency_code', 'code');
    }

    public function carts()
    {
        return $this->hasMany(Cart::class, 'currency_code', 'code');
    }
}

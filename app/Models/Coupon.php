<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class Coupon extends Model implements Auditable
{
    use \App\Models\Traits\Auditable, HasFactory;

    protected $fillable = [
        'type',
        'time',
        'code',
        'value',
        'max_uses',
        'max_uses_per_user',
        'starts_at',
        'expires_at',
        'recurring',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'max_uses' => 'integer',
        'max_uses_per_user' => 'integer',
    ];

    /**
     * Get the products that belong to the option.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'coupon_products');
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Check if the user has exceeded the maximum allowed uses of this coupon
     *
     * @param  int  $userId
     */
    public function hasExceededMaxUsesPerUser($userId): bool
    {
        if (empty($this->max_uses_per_user)) {
            return false;
        }

        return $this->services()
            ->where('user_id', $userId)
            ->count() >= $this->max_uses_per_user;
    }
}

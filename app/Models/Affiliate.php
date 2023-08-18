<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Affiliate extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'visitors',
    ];

    public function getCommissionAttribute()
    {
        return config('settings::affiliate_percentage',0);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function affiliateUsers()
    {
        return $this->hasMany(AffiliateUser::class);
    }

    public function earnings()
    {
        $earnings = 0;
        foreach($this->affiliateUsers as $affiliateUser) {
            $earnings += $affiliateUser->earnings();
        }

        return $earnings;
    }
}

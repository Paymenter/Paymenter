<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Utils\Permissions;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'first_name',
        'last_name',
        'email',
        'password',
        'address',
        'city',
        'state',
        'zip',
        'country',
        'phone',
        'companyname',
        'tfa_secret',
        'role_id',
        'credits',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'permissions',
        'tfa_secret',
        'credits',
    ];

    /**
     * The default with relationships
     * 
     * @var array<int, string>
     */
    protected $with = [
        'role',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'permissions' => 'array',
    ];

    protected $appends = ['name'];

    // If role_id is null, set to 2 (client)
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->role_id = $user->role_id ?? 2;
        });

        static::deleting(function ($user) {
            foreach ($user->orders as $order) {
                $order->products()->delete();
            }
            $user->orders()->delete();
            $user->tickets()->delete();
            $user->invoices()->delete();
            EmailLog::where('user_id', $user->id)->delete();
        });
    }

    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'user_id', 'id');
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    /**
     * Get all OrderProducts for this user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function orderProducts()
    {
        return $this->hasManyThrough(OrderProduct::class, Order::class, 'user_id', 'order_id', 'id', 'id');
    }

    public function has($permission)
    {
        return (new Permissions($this->role->permissions))->has($permission);
    }

    public function formattedCredits()
    {
        return number_format($this->credits, 2);
    }

    public function affiliate()
    {
        return $this->hasOne(Affiliate::class);
    }

    public function affiliateUser()
    {
        return $this->hasOne(AffiliateUser::class);
    }
}

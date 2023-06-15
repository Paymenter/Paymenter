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
        'name',
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
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'is_admin',
        'permissions',
        'tfa_secret',
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

    // If role_id is null, set to 2 (client)
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->role_id = $user->role_id ?? 2;
        });
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'client', 'id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'client', 'id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'user_id', 'id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    public function has($permission)
    {
        return (new Permissions($this->role->permissions))->has($permission);
    }
}

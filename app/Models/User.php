<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Observers\UserObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([UserObserver::class])]
class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'role_id',
        'tfa_secret',
        'credits',
        'address',
        'address2',
        'city',
        'state',
        'zip',
        'country',
        'phone',
        'company_name',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'tfa_secret',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'tfa_secret' => 'encrypted',
        ];
    }

    /**
     * Initials of the user.
     * 
     * @return string
     */
    public function initials(): Attribute
    {
        return Attribute::make(
            get: fn () => strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1)),
        );    
    }

    /**
     * Avatar URL for the user.
     * 
     * @return string
     */
    public function avatar(): Attribute
    {
        return Attribute::make(
            get: fn () => 'https://www.gravatar.com/avatar/' . md5(strtolower($this->email)) . '?d=' . urlencode(config('settings.gravatar_default')),
        );
    }

    /**
     * Get the display name for the user. 
     *
     * @return string
     */
    public function name(): Attribute
    {
        return Attribute::make(
            get: fn () => ($this->first_name . ' ' . $this->last_name) ?: $this->email,
        );
    }

    public function hasPermission($permission): bool
    {
        if (is_null($this->role)) {
            return false;
        }

        // If the user has all permissions, return true
        if (in_array('*', $this->role->permissions)) {
            return true;
        }

        return in_array($permission, $this->role->permissions);
    }

    /** Relationships */
    /**
     * Get the role that the user belongs to.
     * Can be null if the user is a normal user (non-admin).
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the user's sessions.
     */
    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    /**
     * Get the user's services.
     */
    public function services()
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Get the user's audit logs.
     */
    public function audits()
    {
        return $this->morphMany(AuditLog::class, 'model');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return !is_null($this->role);
        }

        return false;
    }
}

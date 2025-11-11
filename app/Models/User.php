<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Traits\HasProperties;
use App\Observers\UserObserver;
use Dedoc\Scramble\Attributes\SchemaName;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use OwenIt\Auditing\Contracts\Auditable;

#[SchemaName('UserModel')]
#[ObservedBy([UserObserver::class])]
class User extends Authenticatable implements Auditable, FilamentUser, HasAvatar
{
    use \App\Models\Traits\Auditable, HasApiTokens, HasFactory, HasProperties, Notifiable;

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
        'email_verified_at',
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
            get: fn () => 'https://www.gravatar.com/avatar/' . md5(strtolower($this->email)) . '?d=' . urlencode((string) config('settings.gravatar_default')),
        );
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar;
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
     * Get the user's orders.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the user's services
     */
    public function services()
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Get the user's invoices.
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return !is_null($this->role);
        }

        return false;
    }

    /**
     * Get the user tickets
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Get the user's credits
     */
    public function credits()
    {
        return $this->hasMany(Credit::class);
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function billingAgreements()
    {
        return $this->hasMany(BillingAgreement::class);
    }

    public function transactions()
    {
        return $this->hasManyThrough(InvoiceTransaction::class, Invoice::class, 'user_id', 'invoice_id', 'id', 'id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function notificationsPreferences()
    {
        return $this->hasMany(NotificationPreference::class);
    }

    public function pushSubscriptions()
    {
        return $this->hasMany(NotificationSubscription::class);
    }

    public function authenticationLogs()
    {
        return $this->hasMany(UserAuthenticationLog::class);
    }
}

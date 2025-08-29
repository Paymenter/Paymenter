<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session as FacadesSession;
use Throwable;

class Session extends Model
{
    use HasFactory;

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'user_agent',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'last_activity' => 'datetime',
        ];
    }

    public function impersonating(): bool
    {
        try {
            $payload = $this->payload;

            $decoded = config('session.encrypt')
                ? Crypt::decryptString($payload)
                : base64_decode($payload);

            $data = unserialize($decoded);

            return !empty($data['impersonating']);
        } catch (Throwable $e) {
            return false;
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getIsCurrentDeviceAttribute()
    {
        return $this->id === FacadesSession::getId();
    }

    public function getFormattedLastActiveAttribute()
    {
        return $this->last_activity->diffForHumans();
    }

    public function getIsMobileAttribute()
    {
        return preg_match('/(android|webos|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i', $this->user_agent);
    }

    public function getFormattedDeviceAttribute()
    {
        if (preg_match('/Linux/i', $this->user_agent)) {
            $os = 'Linux';
        } elseif (preg_match('/Mac/i', $this->user_agent)) {
            $os = 'Mac';
        } elseif (preg_match('/iPhone/i', $this->user_agent)) {
            $os = 'iPhone';
        } elseif (preg_match('/iPad/i', $this->user_agent)) {
            $os = 'iPad';
        } elseif (preg_match('/Droid/i', $this->user_agent)) {
            $os = 'Droid';
        } elseif (preg_match('/Unix/i', $this->user_agent)) {
            $os = 'Unix';
        } elseif (preg_match('/Windows/i', $this->user_agent)) {
            $os = 'Windows';
        } else {
            $os = 'Unknown';
        }

        // Browser Detection

        if (preg_match('/Firefox/i', $this->user_agent)) {
            $br = 'Firefox';
        } elseif (preg_match('/Mac/i', $this->user_agent)) {
            $br = 'Mac';
        } elseif (preg_match('/Chrome/i', $this->user_agent)) {
            $br = 'Chrome';
        } elseif (preg_match('/Opera/i', $this->user_agent)) {
            $br = 'Opera';
        } elseif (preg_match('/MSIE/i', $this->user_agent)) {
            $br = 'IE';
        } else {
            $br = 'Unknown';
        }

        return "{$os} - {$br}";
    }
}

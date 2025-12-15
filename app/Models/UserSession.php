<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class UserSession extends Model
{
    use HasUlids;

    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'last_activity',
        'expires_at',
    ];

    public $timestamps = false;

    protected $casts = [
        'last_activity' => 'datetime',
        'expires_at' => 'datetime',
    ];

    // Update last activity every 60 seconds
    private const LAST_ACTIVITY_UPDATE = 60;

    public function uniqueIds()
    {
        return [
            'ulid',
        ];
    }

    public static function findValid(string $ulid): ?self
    {
        $now = Carbon::now();

        return self::where('ulid', $ulid)
            ->where(function ($query) use ($now) {
                // Remember sessions: check expires_at
                $query->where(function ($q) use ($now) {
                    $q->whereNotNull('expires_at')
                        ->where('expires_at', '>', $now);
                })
                    // Normal sessions: check inactivity timeout (120 minutes)
                    ->orWhere(function ($q) use ($now) {
                        $q->whereNull('expires_at')
                            ->where('last_activity', '>', $now->copy()->subMinutes(config('session.lifetime')));
                    });
            })
            ->first();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function touchRequest(Request $request)
    {
        $now = Carbon::now();

        if ($this->last_activity === null || $this->last_activity->diffInSeconds($now) >= self::LAST_ACTIVITY_UPDATE) {
            $this->last_activity = $now;
        }

        $this->ip_address = $request->ip();
        $this->user_agent = substr($request->userAgent() ?? '', 0, 512);

        return $this->save();
    }

    public function getIsCurrentDeviceAttribute()
    {
        return $this->id === request()->attributes->get('user_session')->id;
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
        } elseif (preg_match('/Chrome/i', $this->user_agent)) {
            $br = 'Chrome';
        } elseif (preg_match('/Safari/i', $this->user_agent)) {
            $br = 'Safari';
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

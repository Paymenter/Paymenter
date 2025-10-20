<?php

namespace App\Livewire\Client;

use App\Classes\Settings;
use App\Livewire\Component;
use App\Models\NotificationTemplate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class Notifications extends Component
{
    public $preferences = [];

    public function mount()
    {
        foreach ($this->notifications as $notification) {
            $this->preferences[$notification->key] = [
                'mail_enabled' => $notification->mail_enabled,
                'in_app_enabled' => $notification->in_app_enabled,
            ];
        }
    }

    public function savePreferences()
    {
        foreach ($this->notifications as $preference) {
            // Check if allowed to change
            if (!($preference->mail_controllable || $preference->in_app_controllable)) {
                continue;
            }

            // Create new preference
            Auth::user()->notificationsPreferences()->updateOrCreate(
                ['notification_template_id' => $preference->id],
                [
                    'mail_enabled' => $this->preferences[$preference->key]['mail_enabled'],
                    'in_app_enabled' => $this->preferences[$preference->key]['in_app_enabled'],
                ]
            );
        }

        $this->notify('Notification preferences updated successfully.', 'success');
    }

    public function storePushSubscription($subscription)
    {
        $subscription = json_decode($subscription, true);

        $pushSubscription = Auth::user()->pushSubscriptions()
            ->updateOrCreate([
                'endpoint' => $subscription['endpoint'],
            ], [
                'p256dh_key' => $subscription['keys']['p256dh'],
                'auth_key' => $subscription['keys']['auth'],
            ]);

        $this->notify('Push subscription saved successfully.', 'success');

        try {
            $this->sendTestNotification($pushSubscription);
            $this->notify('Test push notification sent successfully. Please check your device.', 'success');
        } catch (\Exception $e) {
            // Failed to send notification
            $this->notify('Failed to send test push notification: ' . $e->getMessage(), 'error');
        }
    }

    private function sendTestNotification($pushSubscription)
    {
        $webPush = new WebPush([
            'VAPID' => [
                'subject' => config('app.url'),
                'publicKey' => config('settings.vapid_public_key'),
                'privateKey' => config('settings.vapid_private_key'),
            ],
        ]);

        // Create the subscription object properly
        $result = $webPush->sendOneNotification(
            $pushSubscription->subscription(),
            json_encode([
                'title' => 'Push Notifications Enabled!',
                'body' => 'You will now receive push notifications from ' . config('app.name'),
                'icon' => Storage::url(config('settings.logo')),
                'badge' => Storage::url(config('settings.logo')),
                'show_in_app' => false,
                // This forces it to be a push notification only
                'show_as_push' => true,
                'data' => [
                    'url' => url('/'),
                ],
            ])
        );

        // Check if the notification was sent successfully
        if (!$result->isSuccess()) {
            throw new \Exception('Push notification failed: ' . $result->getReason());
        }
    }

    #[Computed]
    public function supportsPush()
    {
        return Settings::validateOrCreateVapidKeys();
    }

    #[Computed]
    public function notifications()
    {
        $userPreferences = Auth::user()->notificationsPreferences;

        return NotificationTemplate::where('enabled', true)
            ->where(function ($query) {
                // If they are both force, or force and never, they are not user configurable
                $query->where('mail_enabled', '!=', 'force')
                    ->orWhere('in_app_enabled', '!=', 'force');
            })
            ->whereNotIn('key', [
                'email_verification',
                'password_reset',
                'new_login_detected',
            ])
            ->get()
            ->map(function ($notification) use ($userPreferences) {
                return (object) [
                    'id' => $notification->id,
                    'key' => $notification->key,
                    'name' => $notification->edit_preference_message,
                    'mail_controllable' => $notification->isEmailUserControllable(),
                    'in_app_controllable' => $notification->isInAppUserControllable(),
                    'mail_enabled' => $notification->isEnabledForPreference($userPreferences->firstWhere('notification_template_id', $notification->id), 'mail'),
                    'in_app_enabled' => $notification->isEnabledForPreference($userPreferences->firstWhere('notification_template_id', $notification->id), 'app'),
                ];
            });
    }

    public function render()
    {
        return view('client.account.notifications')->layoutData([
            'sidebar' => true,
            'title' => 'Notifications',
        ]);
    }
}

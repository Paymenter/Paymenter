<?php

namespace App\Listeners;

use App\Events\Notification\Created;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class NotificationCreatedListener implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(Created $event): void
    {
        $notification = $event->notification;

        if ($notification->user->pushSubscriptions->isEmpty()) {
            return;
        }
        // Send as push notification
        $webPush = new WebPush([
            'VAPID' => [
                'subject' => config('app.url'),
                'publicKey' => config('settings.vapid_public_key'),
                'privateKey' => config('settings.vapid_private_key'),
            ],
        ]);

        foreach ($notification->user->pushSubscriptions as $subscription) {
            $webPush->queueNotification(
                $subscription->subscription(),
                json_encode([
                    'title' => $notification->title,
                    'body' => $notification->body,
                    'icon' => Storage::url(config('settings.logo')),
                    'badge' => Storage::url(config('settings.logo')),
                    'url' => $notification->url,
                    'show_in_app' => $notification->show_in_app,
                    'show_as_push' => $notification->show_as_push,
                ]),
            );
        }

        // Send all notifications
        foreach ($webPush->flush() as $report) {
            $endpoint = $report->getRequest()->getUri()->__toString();
            if (!$report->isSuccess() && $report->isSubscriptionExpired()) {
                // Remove expired subscription from database
                $subscription = $notification->user->pushSubscriptions()
                    ->where('endpoint', $endpoint)
                    ->first();
                if ($subscription) {
                    $subscription->delete();
                }
            }
        }
    }
}

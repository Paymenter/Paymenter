<?php

namespace App\Console\Commands;

use App\Enums\DomainStatus;
use App\Helpers\NotificationHelper;
use App\Models\Domain;
use App\Models\DomainNotice;
use Illuminate\Console\Command;

class DomainNotices extends Command
{
    protected $signature = 'app:domain-notices';

    protected $description = 'Send domain expiry and grace period notices';

    public function handle(): int
    {
        $days = array_filter(array_map('intval', explode(',', config('settings.domain_notice_days', '30,14,3'))));
        $sent = 0;

        Domain::query()
            ->with('service.user')
            ->whereNotNull('expires_at')
            ->whereIn('status', [
                DomainStatus::Active->value,
                DomainStatus::Expired->value,
                DomainStatus::Grace->value,
                DomainStatus::Redemption->value,
            ])
            ->each(function (Domain $domain) use ($days, &$sent) {
                $user = $domain->service?->user;
                if (!$user) {
                    return;
                }

                $daysLeft = now()->startOfDay()->diffInDays($domain->expires_at->startOfDay(), false);

                foreach ($days as $day) {
                    if ((int) $daysLeft === $day && $this->send($domain, 'domain_renewal_upcoming', 'days-' . $day, $user, ['days' => $day])) {
                        $sent++;
                    }
                }

                if ($domain->status === DomainStatus::Expired && $this->send($domain, 'domain_expired', 'expired', $user)) {
                    $sent++;
                }

                if ($domain->status === DomainStatus::Grace && $this->send($domain, 'domain_grace_warning', 'grace', $user)) {
                    $sent++;
                }

                if ($domain->status === DomainStatus::Redemption && $this->send($domain, 'domain_redemption_warning', 'redemption', $user)) {
                    $sent++;
                }
            });

        $this->info("Sent {$sent} domain notices.");

        return self::SUCCESS;
    }

    private function send(Domain $domain, string $key, string $stage, $user, array $data = []): bool
    {
        $notice = DomainNotice::firstOrCreate(
            [
                'domain_id' => $domain->id,
                'key' => $key,
                'stage' => $stage,
                'cycle' => $domain->expires_at?->toDateString() ?? '',
            ],
            ['sent_at' => now()]
        );

        if (!$notice->wasRecentlyCreated) {
            return false;
        }

        NotificationHelper::sendNotification($key, array_merge(['domain' => $domain], $data), $user);

        return true;
    }
}

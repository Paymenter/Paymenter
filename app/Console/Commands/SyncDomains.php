<?php

namespace App\Console\Commands;

use App\Enums\DomainStatus;
use App\Models\Domain;
use App\Services\Domain\DomainService;
use Illuminate\Console\Command;
use Throwable;

class SyncDomains extends Command
{
    protected $signature = 'app:domain-sync {--limit=50}';

    protected $description = 'Reconcile domains with their registrar';

    public function handle(): int
    {
        $count = 0;

        Domain::query()
            ->whereIn('status', array_map(
                fn (DomainStatus $status) => $status->value,
                [
                    DomainStatus::Pending,
                    DomainStatus::Active,
                    DomainStatus::Expired,
                    DomainStatus::Grace,
                    DomainStatus::Redemption,
                    DomainStatus::TransferInPending,
                    DomainStatus::TransferOutPending,
                ]
            ))
            ->where(function ($query) {
                $query->whereNull('last_synced_at')->orWhere('last_synced_at', '<', now()->subHours(6));
            })
            ->orderBy('last_synced_at')
            ->limit((int) $this->option('limit'))
            ->get()
            ->each(function (Domain $domain) use (&$count) {
                $registrar = DomainService::registrarForDomain($domain);

                if (!$registrar) {
                    $domain->update([
                        'sync_error' => 'No registrar is available for this domain.',
                        'last_synced_at' => now(),
                    ]);

                    return;
                }

                try {
                    DomainService::apply($domain, $registrar->getInfo($domain), 'sync');
                    $count++;
                } catch (Throwable $e) {
                    $domain->update([
                        'sync_error' => mb_substr($e->getMessage(), 0, 2000),
                        'last_synced_at' => now(),
                    ]);
                }
            });

        $this->info("Synced {$count} domains.");

        return self::SUCCESS;
    }
}

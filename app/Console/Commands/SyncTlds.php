<?php

namespace App\Console\Commands;

use App\Models\Server;
use App\Services\Domain\DomainService;
use Illuminate\Console\Command;

class SyncTlds extends Command
{
    protected $signature = 'app:domain-tlds {--server= : Only sync this server ID}';

    protected $description = 'Import TLD costs from registrar extensions without changing sell prices';

    public function handle(): int
    {
        $servers = Server::query()
            ->when($this->option('server'), fn ($query) => $query->whereKey($this->option('server')))
            ->get();

        $tlds = 0;

        foreach ($servers as $server) {
            $tlds += DomainService::syncTldsForServer($server);
        }

        $this->info("Synced {$tlds} TLD costs.");

        return self::SUCCESS;
    }
}

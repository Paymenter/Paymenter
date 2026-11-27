<?php

namespace App\Events\Domain;

use App\Enums\DomainStatus;
use App\Models\Domain;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Transitioned
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Domain $domain,
        public DomainStatus $from,
        public DomainStatus $to,
        public string $reason,
    ) {}
}

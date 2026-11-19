<?php

namespace App\Events\Domain;

use App\Models\Domain;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Failed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Domain $domain,
        public string $error,
        public string $operation = 'registration',
    ) {}
}

<?php

namespace App\Observers;

use App\Events\Domain\Created;
use App\Events\Domain\Deleted;
use App\Events\Domain\Updated;
use App\Models\Domain;

class DomainObserver
{
    public function created(Domain $domain): void
    {
        event(new Created($domain));
    }

    public function updated(Domain $domain): void
    {
        event(new Updated($domain));
    }

    public function deleted(Domain $domain): void
    {
        event(new Deleted($domain));
    }
}

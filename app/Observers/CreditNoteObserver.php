<?php

namespace App\Observers;

use App\Events\CreditNote as CreditNoteEvent;
use App\Models\CreditNote;

class CreditNoteObserver
{
    /**
     * Handle the CreditNote "creating" event.
     */
    public function creating(CreditNote $creditNote): void
    {
        event(new CreditNoteEvent\Creating($creditNote));
    }
}

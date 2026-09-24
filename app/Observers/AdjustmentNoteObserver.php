<?php

namespace App\Observers;

use App\Events\AdjustmentNote as AdjustmentNoteEvent;
use App\Models\AdjustmentNote;

class AdjustmentNoteObserver
{
    /**
     * Handle the AdjustmentNote "creating" event.
     */
    public function creating(AdjustmentNote $adjustmentNote): void
    {
        event(new AdjustmentNoteEvent\Creating($adjustmentNote));
    }
}

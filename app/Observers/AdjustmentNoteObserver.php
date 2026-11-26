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

    /**
     * Handle the AdjustmentNote "created" event.
     */
    public function created(AdjustmentNote $adjustmentNote): void
    {
        event(new AdjustmentNoteEvent\Created($adjustmentNote));
    }

    /**
     * Handle the AdjustmentNote "updated" event.
     */
    public function updated(AdjustmentNote $adjustmentNote): void
    {
        event(new AdjustmentNoteEvent\Updated($adjustmentNote));
    }
}

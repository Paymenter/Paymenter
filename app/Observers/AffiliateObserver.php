<?php

namespace App\Observers;

use App\Models\Affiliate;

class AffiliateObserver
{
    /**
     * Handle the Affiliate "created" event.
     *
     * @return void
     */
    public function created(Affiliate $affiliate)
    {
        //
    }

    /**
     * Handle the Affiliate "updated" event.
     *
     * @return void
     */
    public function updated(Affiliate $affiliate)
    {
        //
    }

    /**
     * Handle the Affiliate "deleted" event.
     *
     * @return void
     */
    public function deleted(Affiliate $affiliate)
    {
        //
    }

    /**
     * Handle the Affiliate "restored" event.
     *
     * @return void
     */
    public function restored(Affiliate $affiliate)
    {
        //
    }

    /**
     * Handle the Affiliate "force deleted" event.
     *
     * @return void
     */
    public function forceDeleted(Affiliate $affiliate)
    {
        //
    }
}

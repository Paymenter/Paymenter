<?php

namespace App\Observers;

use App\Events\Announcement\AnnouncementCreated;
use App\Models\Announcement;

class AnnouncementObserver
{
    /**
     * Handle the Announcement "created" event.
     *
     * @param  \App\Models\Announcement  $announcement
     * @return void
     */
    public function created(Announcement $announcement)
    {
        event(new AnnouncementCreated($announcement));
    }

    /**
     * Handle the Announcement "updated" event.
     *
     * @param  \App\Models\Announcement  $announcement
     * @return void
     */
    public function updated(Announcement $announcement)
    {
        //
    }

    /**
     * Handle the Announcement "deleted" event.
     *
     * @param  \App\Models\Announcement  $announcement
     * @return void
     */
    public function deleted(Announcement $announcement)
    {
        //
    }

    /**
     * Handle the Announcement "restored" event.
     *
     * @param  \App\Models\Announcement  $announcement
     * @return void
     */
    public function restored(Announcement $announcement)
    {
        //
    }

    /**
     * Handle the Announcement "force deleted" event.
     *
     * @param  \App\Models\Announcement  $announcement
     * @return void
     */
    public function forceDeleted(Announcement $announcement)
    {
        //
    }
}

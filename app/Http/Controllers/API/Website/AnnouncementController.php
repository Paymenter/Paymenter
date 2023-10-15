<?php

namespace App\Http\Controllers\API\Website;

use App\Classes\API;
use App\Http\Controllers\Controller;
use App\Models\Announcement;

class AnnouncementController extends Controller
{
    /**
     * Get all Announcements.
     */
    public function getAnnouncements()
    {
        $announcements = Announcement::paginate(25);

        return response()->json([
            'announcements' => API::repaginate($announcements),
        ], 200);
    }

    /**
     * Get a Announcement.
     */
    public function getAnnouncement(string $id)
    {
        return response()->json([
            'announcement' => Announcement::findOrFail($id),
        ], 200);
    }
}

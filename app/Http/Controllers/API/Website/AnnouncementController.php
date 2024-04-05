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
        $announcements = Announcement::where('published', 1)->paginate(25);

        return response()->json([
            'announcements' => API::repaginate($announcements),
        ], 200);
    }

    /**
     * Get a Announcement.
     */
    public function getAnnouncement(string $id)
    {
        if (!$announcement = Announcement::where('id', $id)->where('published', 1)->first()) {
            return response()->json([
                'error' => 'Announcement not found.',
            ], 404);
        }
        return response()->json([
            'announcement' => $announcement,
        ], 200);
    }
}

<?php
namespace App\Http\Controllers;

use App\Models\{Announcement, Category, Product};
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::where('published', 1)->get();

        return view('announcement.index', compact('announcements'));
    }

    public function view(Announcement $announcement)
    {
        if (!$announcement->published) {
            abort(404);
        }
        return view('announcement.view', compact('announcement'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Models\Announcement;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnnouncementController extends Controller
{

    /**
     * Display a listing of the announcements
     *
     * @return View
     */
    public function index(): View
    {
        $announcements = Announcement::all();

        return view('admin.announcements.index', compact('announcements'));
    }

    /**
     * Display creating form
     *
     * @return View
     */
    public function create(): View
    {
        return view('admin.announcements.create');
    }

    /**
     * Store a new announcement
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $data = request()->validate([
            'title' => 'required|string|min:4',
            'announcement' => 'required',
            'published' => 'required|boolean',
        ]);

        Announcement::create($data);

        if ($request->has('published')) {
            $data['published'] = true;
        } else {
            $data['published'] = false;
        }

        return redirect()->route('admin.announcements.edit', Announcement::latest()->first())->with('success', 'Announcement created successfully!');
    }

    public function edit(Announcement $announcement)
    {
        return view('admin.announcements.edit', compact('announcement'));
    }

    public function update(Announcement $announcement, Request $request)
    {
        $data = request()->validate([
            'title' => 'required|between:3,255',
            'announcement' => 'required',
        ]);

        if($request->has('published')) {
            $data['published'] = true;
        } else {
            $data['published'] = false;
        }

        $announcement->update($data);

        return redirect()->route('admin.announcements')->with('success', 'Announcement updated successfully!');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return redirect()->route('admin.announcements')->with('success', 'Announcement deleted successfully!');
    }
}

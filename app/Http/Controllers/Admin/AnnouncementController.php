<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the announcements
     */
    public function index(): View
    {
        return view('admin.announcements.index');
    }

    /**
     * Display creating form
     */
    public function create(): View
    {
        return view('admin.announcements.create');
    }

    /**
     * Store a new announcement
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedRequest = $request->validate([
            'title' => 'required|string|min:4',
            'announcement' => 'string|required|min:4',
            'published' => 'sometimes',
        ]);

        $request->has('published') ? $validatedRequest['published'] = true : $validatedRequest['published'] = false;

        Announcement::create($validatedRequest);

        return redirect()->route('admin.announcements.edit', Announcement::latest()->first())->with('success', 'Announcement created successfully!');
    }

    /**
     * Display the announcement edit form
     */
    public function edit(Announcement $announcement): View
    {
        return view('admin.announcements.edit', compact('announcement'));
    }

    /**
     * Update the announcement
     */
    public function update(Announcement $announcement, Request $request): RedirectResponse
    {
        $validatedRequest = $request->validate([
            'title' => 'required|string|min:4',
            'announcement' => 'string|required|min:4',
            'published' => 'sometimes',
        ]);

        $request->has('published') ? $validatedRequest['published'] = true : $validatedRequest['published'] = false;

        $announcement->update($validatedRequest);

        return redirect()->route('admin.announcements.edit', $announcement)->with('success', 'Announcement updated successfully!');
    }

    /**
     * Delete the announcement
     */
    public function destroy(Announcement $announcement): RedirectResponse
    {
        $announcement->delete();

        return redirect()->route('admin.announcements')->with('success', 'Announcement deleted successfully!');
    }
}

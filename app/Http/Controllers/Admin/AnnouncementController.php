<?php

namespace App\Http\Controllers\Admin;

use App\Models\Announcement;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::all();

        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('admin.announcements.create');
    }

    public function store(Request $request)
    {
        $data = request()->validate([
            'title' => 'required',
            'announcement' => 'required',
            'published' => 'required',
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
<?php

namespace Paymenter\Extensions\Others\Announcements\Livewire\Announcements;

use Livewire\Component;
use Paymenter\Extensions\Others\Announcements\Models\Announcement;

class Widget extends Component
{
    public function mount()
    {
        if (Announcement::where('is_active', true)->where('published_at', '<=', now())->count() == 0) {
            return abort(404);
        }
    }

    public function render()
    {
        return view('announcements::widget', [
            'announcements' => Announcement::latest()->get(),
        ]);
    }
}

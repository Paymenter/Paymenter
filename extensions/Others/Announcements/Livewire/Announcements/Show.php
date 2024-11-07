<?php

namespace Paymenter\Extensions\Others\Announcements\Livewire\Announcements;

use Livewire\Component;
use Paymenter\Extensions\Others\Announcements\Models\Announcement;

class Show extends Component
{
    public Announcement $announcement;

    public function mount()
    {
        if (!$this->announcement->is_active || $this->announcement->published_at > now()) {
            return abort(404);
        }
    }

    public function render()
    {
        return view('announcements::show');
    }
}

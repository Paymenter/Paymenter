<?php

namespace App\Livewire\Tickets;

use App\Livewire\Component;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class Widget extends Component
{
    use WithPagination;

    public function render()
    {
        return view('tickets.widget', [
            'tickets' => Ticket::where('user_id', Auth::id())->where('status', '!=', 'closed')->latest()->paginate(config('settings.pagination')),
        ])->layoutData([
            'title' => 'Tickets',
        ]);
    }
}

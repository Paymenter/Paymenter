<?php

namespace App\Livewire\Tickets;

use App\Livewire\Component;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public function render()
    {
        return view('tickets.index', [
            'tickets' => Ticket::where('user_id', Auth::id())->latest()->paginate(config('settings.pagination')),
        ]);
    }
}

<?php

namespace App\Livewire\Services;

use App\Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public function render()
    {
        return view('services.index', [
            'services' => Auth::user()->services()->where('status', '!=', 'cancelled')->paginate(config('settings.pagination')),
        ])->layoutData([
            'title' => 'Services',
        ]);
    }
}

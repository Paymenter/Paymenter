<?php

namespace App\Livewire\Domains;

use App\Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public function render()
    {
        return view('domains.index', [
            'domains' => Auth::user()->domains()->with('service')->orderByDesc('created_at')->paginate(config('settings.pagination')),
        ])->layoutData([
            'title' => 'Domains',
            'sidebar' => true,
        ]);
    }
}

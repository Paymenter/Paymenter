<?php

namespace App\Livewire\Services;

use App\Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $status = null;

    public function render()
    {
        $query = Auth::user()->services();

        if ($this->status) {
            $query->where('status', $this->status);
        } else {
            $query->where('status', '!=', 'cancelled');
        }

        return view('services.index', [
            'services' => $query->paginate(config('settings.pagination')),
        ])->layoutData([
            'title' => 'Services',
        ]);
    }
}

<?php

namespace App\Livewire\Invoices;

use App\Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public function render()
    {
        return view('invoices.index', [
            'invoices' => Auth::user()->invoices()->with(['user', 'snapshot', 'items'])->orderBy('id', 'desc')->paginate(config('settings.pagination')),
        ])->layoutData([
            'title' => __('invoices.invoices'),
            'sidebar' => true,
        ]);
    }
}

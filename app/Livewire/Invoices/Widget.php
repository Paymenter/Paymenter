<?php

namespace App\Livewire\Invoices;

use App\Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class Widget extends Component
{
    use WithPagination;

    public function render()
    {
        return view('invoices.widget', [
            'invoices' => Auth::user()->invoices()->orderBy('id', 'desc')->where('status', '=', 'pending')->paginate(config('settings.pagination')),
        ])->layoutData([
            'title' => __('invoices.invoices'),
        ]);
    }
}

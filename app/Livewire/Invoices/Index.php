<?php

namespace App\Livewire\Invoices;

use App\Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $status = '';

    public function updatedStatus()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Auth::user()->invoices()->with(['user', 'snapshot', 'items']);

        // Filters
        if ($this->status === 'paid') {
            $query->where('status', 'paid'); 
        } elseif ($this->status === 'pending') {
            $query->where('status', 'pending');
        } elseif ($this->status === 'cancelled') {
            $query->where('status', 'cancelled'); }

        return view('invoices.index', [
            'invoices' => $query
                ->orderBy('created_at', 'desc')
                ->paginate(config('settings.pagination')),
        ])->layoutData([
            'title' => __('invoices.invoices'),
            'sidebar' => true,
        ]);
    }
}

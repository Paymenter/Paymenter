<?php

namespace App\Livewire\Invoices;

use App\Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public ?string $status = null;

    #[Validate('nullable|date')]
    #[Url]
    public ?string $dateFrom = null;

    #[Validate('nullable|date|after_or_equal:dateFrom')]
    #[Url]
    public ?string $dateUntil = null;

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedDateFrom(): void
    {
        $this->validateOnly('dateFrom');
        $this->validateOnly('dateUntil');
        $this->resetPage();
    }

    public function updatedDateUntil(): void
    {
        $this->validateOnly('dateUntil');
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['status', 'dateFrom', 'dateUntil']);
        $this->resetPage();
    }

    public function render()
    {
        $query = Auth::user()->invoices()->with(['user', 'snapshot', 'items', 'transactions'])->orderBy('id', 'desc');

        if (in_array($this->status, ['pending', 'paid', 'cancelled'], true)) {
            $query->where('status', $this->status);
        }

        if ($this->dateFrom && strtotime($this->dateFrom)) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateUntil && strtotime($this->dateUntil)) {
            $query->whereDate('created_at', '<=', $this->dateUntil);
        }

        return view('invoices.index', [
            'invoices' => $query->paginate(config('settings.pagination')),
        ])->layoutData([
            'title' => __('invoices.invoices'),
            'sidebar' => true,
        ]);
    }
}

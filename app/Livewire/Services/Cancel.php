<?php

namespace App\Livewire\Services;

use App\Livewire\Component;
use App\Models\Service;
use App\Models\ServiceCancellation;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;

class Cancel extends Component
{
    public Service $service;

    #[Validate('required|in:end_of_period,immediate')]
    public $type = 'end_of_period';

    #[Validate('required|max:255')]
    public $reason = '';

    public function mount()
    {
        // A service without an expiry date (e.g. an unpaid order) has no billing period to wait for
        if (!$this->service->expires_at) {
            $this->type = 'immediate';
        }
    }

    public function cancelService()
    {
        $this->authorize('view', $this->service);

        $this->validate();

        // The form is also reachable without the cancellable check on the button (?cancel=1, a second tab)
        $cancelled = DB::transaction(function () {
            $service = Service::whereKey($this->service->id)->lockForUpdate()->first();
            if (!$service?->cancellable) {
                return false;
            }

            // Event hook will handle the cancellation (if its immediate or end of period)
            ServiceCancellation::create([
                'service_id' => $service->id,
                'type' => $service->expires_at ? $this->type : 'immediate',
                'reason' => $this->reason,
            ]);

            return true;
        });

        if (!$cancelled) {
            $this->notify(__('services.not_cancellable'), 'error', true);

            return $this->redirect(route('services.show', $this->service), true);
        }

        $this->notify(__('services.cancellation_requested'), 'success', true);

        $this->redirect(route('services.show', $this->service), true);
    }

    public function render()
    {
        return view('services.cancel')->layoutData([
            'title' => __('services.cancellation', ['service' => $this->service->product->name]),
            'sidebar' => true,
        ]);
    }
}

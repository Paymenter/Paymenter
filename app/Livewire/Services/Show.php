<?php

namespace App\Livewire\Services;

use App\Enums\InvoiceTransactionStatus;
use App\Helpers\ExtensionHelper;
use App\Livewire\Component;
use App\Models\Invoice;
use App\Models\Service;
use App\Models\ServiceUpgrade;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;

class Show extends Component
{
    public Service $service;

    #[Locked]
    public $buttons = [];

    #[Locked]
    public $views = [];

    #[Locked]
    public $fields = [];

    #[Url('tab', except: false), Locked]
    public $currentView;

    #[Url('cancel', except: false)]
    public bool $showCancel = false;

    public bool $showBillingAgreement = false;

    #[Url('label', except: false)]
    public bool $editLabel = false;

    public ?string $label = null;

    public $selectedMethod;

    public function mount()
    {
        // Only fetch the actions if the service is active
        if ($this->service->status == Service::STATUS_ACTIVE) {
            $actions = [];
            try {
                $actions = ExtensionHelper::getActions($this->service);
            } catch (Exception $e) {
            }
            // separate the actions into buttons and views
            foreach ($actions as $action) {
                if ($action['type'] == 'button') {
                    $this->buttons[] = $action;
                } elseif ($action['type'] == 'view') {
                    $this->views[] = $action;
                } elseif ($action['type'] == 'text') {
                    $this->fields[] = $action;
                }
            }
            $this->currentView = $this->currentView ?? ($this->views[0]['name'] ?? null);
        }
        $this->label = $this->service->label;
    }

    public function cancelUpgrade()
    {
        $this->authorize('view', $this->service);

        $result = DB::transaction(function () {
            $pendingUpgrade = $this->service->upgrade()
                ->where('status', ServiceUpgrade::STATUS_PENDING)
                ->first();

            if (!$pendingUpgrade) {
                return 'not_found';
            }

            $invoice = null;
            if ($pendingUpgrade->invoice_id) {
                $invoice = Invoice::where('id', $pendingUpgrade->invoice_id)
                    ->lockForUpdate()
                    ->first();

                if ($invoice && $invoice->status === Invoice::STATUS_PAID) {
                    return 'invoice_not_pending';
                }

                if ($invoice && $invoice->status === Invoice::STATUS_PENDING) {
                    $hasSucceededTransaction = $invoice->transactions()
                        ->where('status', InvoiceTransactionStatus::Succeeded)
                        ->lockForUpdate()
                        ->exists();
                    if ($hasSucceededTransaction) {
                        return 'invoice_not_pending';
                    }
                }
            }

            $lockedUpgrade = ServiceUpgrade::where('id', $pendingUpgrade->id)
                ->where('status', ServiceUpgrade::STATUS_PENDING)
                ->lockForUpdate()
                ->first();

            if (!$lockedUpgrade) {
                return 'upgrade_gone';
            }

            $lockedUpgrade->status = ServiceUpgrade::STATUS_CANCELLED;
            $lockedUpgrade->save();

            if ($invoice && $invoice->status === Invoice::STATUS_PENDING) {
                $invoice->status = Invoice::STATUS_CANCELLED;
                $invoice->save();
            }

            return 'ok';
        });

        if ($result !== 'ok') {
            $message = match ($result) {
                'invoice_not_pending' => __('services.cancel_upgrade_failed'),
                default => __('services.cancel_upgrade_not_found'),
            };
            $this->notify($message, 'error');

            return;
        }

        $this->notify(__('services.upgrade_cancelled'), 'success');
    }

    public function updatedShowBillingAgreement()
    {
        $this->selectedMethod = Auth::user()->billingAgreements()->where('id', $this->service->billing_agreement_id)?->first()?->ulid;
    }

    public function updateBillingAgreement()
    {
        $agreement = Auth::user()->billingAgreements()->where('ulid', $this->selectedMethod)->first();
        $this->service->billing_agreement_id = $agreement->id;
        $this->service->save();

        $this->showBillingAgreement = false;
    }

    public function clearBillingAgreement()
    {
        $this->service->billing_agreement_id = null;
        $this->service->save();
        $this->selectedMethod = null;
    }

    public function updateLabel()
    {
        $this->validate([
            'label' => 'nullable|string|max:255',
        ]);

        $this->service->label = $this->label;
        $this->service->save();

        $this->editLabel = false;
        $this->notify('Service label updated successfully', 'success');
    }

    public function changeView($view)
    {
        if (!$view) {
            return;
        }
        if ($this->currentView === $view || !in_array($view, array_column($this->views, 'name'))) {
            return $this->skipRender();
        }
        $this->currentView = $view;
    }

    public function updatedShowCancel($value)
    {
        if (!$this->service->cancellable) {
            $this->notify('This service cannot be cancelled', 'error');
            $this->showCancel = false;

            return;
        }
    }

    public function goto($function)
    {
        // Check if function is allowed
        if (!in_array($function, array_column($this->buttons, 'function'))) {
            $this->notify('This action is not allowed', 'error');

            return;
        }
        $result = ExtensionHelper::callService($this->service, $function);
        // If its a response, return it
        if (!is_string($result)) {
            return $result;
        }
        $this->redirect($result);
    }

    public function render()
    {
        $view = null;
        $previousView = $this->currentView;

        if ($this->currentView) {
            try {
                // Search array for the current view
                $currentViewObj = $this->views[array_search($this->currentView, array_column($this->views, 'name'))] ?? null;
                if (!$currentViewObj) {
                    throw new Exception('View not found');
                }
                $view = ExtensionHelper::getView($this->service, $currentViewObj);
            } catch (Exception $e) {
                if ($previousView !== $this->views[0]['name'] ?? null) {
                    $this->notify('Got an error while trying to load the view', 'error');
                }
                $this->currentView = $this->views[0]['name'] ?? null;
            }
        }

        return view('services.show', ['extensionView' => $view])->layoutData([
            'title' => 'Services',
            'sidebar' => true,
        ]);
    }
}

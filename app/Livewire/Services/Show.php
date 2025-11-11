<?php

namespace App\Livewire\Services;

use App\Helpers\ExtensionHelper;
use App\Livewire\Component;
use App\Models\Service;
use Exception;
use Illuminate\Support\Facades\Auth;
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

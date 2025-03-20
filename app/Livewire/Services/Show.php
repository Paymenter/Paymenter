<?php

namespace App\Livewire\Services;

use App\Helpers\ExtensionHelper;
use App\Livewire\Component;
use App\Models\Service;
use Livewire\Attributes\Locked;

class Show extends Component
{
    public Service $service;

    public $buttons = [];

    public $views = [];

    #[Locked]
    public $currentView;

    public $showModal = '';

    public function mount()
    {
        // Only fetch the actions if the service is active
        if ($this->service->status == Service::STATUS_ACTIVE) {
            $actions = [];
            try {
                $actions = ExtensionHelper::getActions($this->service);
            } catch (\Exception $e) {
            }
            // separate the actions into buttons and views
            foreach ($actions as $action) {
                if ($action['type'] == 'button') {
                    $this->buttons[] = $action;
                } elseif ($action['type'] == 'view') {
                    $this->views[] = $action;
                }
            }
            $this->changeView($this->views[0]['name'] ?? null);
        }
    }

    public function changeView($view)
    {
        if (!$view) {
            return;
        }
        $this->currentView = $view;
    }

    public function openModal($modal)
    {
        $this->showModal = $modal;
    }

    public function goto($function)
    {
        // Check if function is allowed
        if (!in_array($function, array_column($this->buttons, 'function'))) {
            $this->notify('This action is not allowed', 'error');

            return;
        }
        $this->redirect(ExtensionHelper::callService($this->service, $function));
    }

    public function render()
    {
        $view = null;
        $previousView = $this->currentView;

        if ($this->currentView) {
            try {
                $view = ExtensionHelper::getView($this->service, $this->currentView);
            } catch (\Exception $e) {
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

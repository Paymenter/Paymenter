<?php

namespace App\Livewire\Traits;

trait HasConfirmation
{
    public function confirmAction($action, $parameters = [], $title = null, $message = null, $confirmText = 'Confirm', $cancelText = 'Cancel')
    {
        $this->dispatch('show-confirmation', [
            'action' => $action,
            'parameters' => $parameters,
            'title' => $title ?? 'Are you sure?',
            'message' => $message ?? 'This action cannot be undone.',
            'confirmText' => $confirmText,
            'cancelText' => $cancelText,
        ]);
    }

    public function executeConfirmedAction($action, $parameters = [])
    {
        if (method_exists($this, $action)) {
            return $this->{$action}(...$parameters);
        }

        throw new \Exception("Method {$action} does not exist");
    }
}

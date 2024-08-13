<?php

namespace App\Livewire;

class Component extends \Livewire\Component
{
    /**
     * Notifications
     */
    public function notify($message, $type = 'success')
    {
        $this->dispatch('notify', ['message' => $message, 'type' => $type]);
    }

    public function paginationView()
    {
        return 'components.pagination';
    }
}

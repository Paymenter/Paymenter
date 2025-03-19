<?php

namespace App\Livewire\Traits;

use Illuminate\Support\Facades\Session;

trait HasNotifications
{
    public function bootHasNotifications()
    {
        if (Session::has('notification')) {
            $this->notify(
                Session::get('notification')['message'],
                Session::get('notification')['type']
            );
            Session::forget('notification');
        }
    }

    /**
     * Notifications
     */
    public function notify($message, $type = 'success')
    {
        $this->dispatch('notify', ['message' => $message, 'type' => $type]);
    }
}

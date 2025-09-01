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
    public function notify($message, $type = 'success', $redirect = false)
    {
        if ($redirect) {
            // Set notification in session before redirect
            Session::put('notification', [
                'message' => $message,
                'type' => $type,
            ]);
        } else {
            $this->dispatch('notify', ['message' => $message, 'type' => $type]);
        }
    }
}

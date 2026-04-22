<?php

namespace App\Livewire\Components;

use App\Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

class Notifications extends Component
{
    #[Computed, On('notification-added')]
    public function notifications()
    {
        return Auth::user()->notifications()->orderBy('created_at', 'desc')->limit(5)->get();
    }

    public function goToNotification($id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();
        if (!$notification) {
            return;
        }

        $notification->markAsRead();

        if ($notification->url) {
            return $this->redirect($notification->url, true);
        }
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();
        if (!$notification) {
            return;
        }

        $notification->markAsRead();
        unset($this->notifications);
    }

    public function markAsUnread($id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();
        if (!$notification) {
            return;
        }

        DB::table('notifications')->where('id', $notification->id)->update(['read_at' => null]);
        unset($this->notifications);
    }

    public function deleteNotification($id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();
        if (!$notification) {
            return;
        }

        $notification->delete();
        unset($this->notifications);
    }

    public function deleteAllNotifications()
    {
        Auth::user()->notifications()->delete();
        unset($this->notifications);
    }

    public function render()
    {
        return view('components.notifications');
    }
}

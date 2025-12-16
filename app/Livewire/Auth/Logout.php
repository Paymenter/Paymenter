<?php

namespace App\Livewire\Auth;

use App\Livewire\Component;

class Logout extends Component
{
    public function logout(\App\Actions\Auth\Logout $logoutAction)
    {
        $logoutAction->execute();

        return redirect('/');
    }

    public function render()
    {
        return view('auth.logout');
    }
}
